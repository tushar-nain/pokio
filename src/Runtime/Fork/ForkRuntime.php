<?php

declare(strict_types=1);

namespace Pokio\Runtime\Fork;

use Closure;
use Pokio\Contracts\Future;
use Pokio\Contracts\Runtime;
use Pokio\Promise;
use RuntimeException;
use Throwable;

/**
 * @internal
 */
final class ForkRuntime implements Runtime
{
    /**
     * The PIDs of the currently running processes.
     *
     * @var array<int, int>
     */
    private static array $processes = [];

    /**
     * Creates a new fork runtime instance.
     */
    public function __construct(private int $maxProcesses)
    {
        //
    }

    /**
     * Cleans up any remaining processes on destruction, if any.
     */
    public function __destruct()
    {
        foreach (self::$processes as $pid) {
            pcntl_waitpid($pid, $status);
        }
    }

    /**
     * Defers the given callback to be executed asynchronously.
     *
     * @template TResult
     *
     * @param  Closure(): TResult  $callback
     * @return Future<TResult>
     */
    public function defer(Closure $callback): Future
    {
        while (count(self::$processes) >= $this->maxProcesses) {
            $this->waitForProcess();
        }

        $ipc = IPC::create();
        $pid = pcntl_fork();

        if ($pid === -1) {
            throw new RuntimeException('Failed to fork process');
        }

        if ($pid === 0) {
            try {
                $result = $callback();

                if ($result instanceof Promise) {
                    $result = await($result);
                }
            } catch (Throwable $exception) {
                $result = new ThrowableCapsule($exception);
            }

            $data = serialize($result);

            $ipc->put($data);

            exit(0);
        }

        self::$processes[] = $pid;

        /** @var Future<TResult> $future */
        $future = new ForkFuture($pid, $ipc); // @phpstan-ignore-line

        return $future;
    }

    /**
     * Waits for a process to finish and removes it from the list of processes.
     */
    private function waitForProcess(): void
    {
        $pid = pcntl_wait($status);
        if ($pid > 0) {
            self::$processes = array_filter(self::$processes, fn (int $p) => $p !== $pid);
        }
    }
}
