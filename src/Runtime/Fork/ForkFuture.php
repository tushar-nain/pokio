<?php

declare(strict_types=1);

namespace Pokio\Runtime\Fork;

use Closure;
use Pokio\Contracts\Future;
use Pokio\Exceptions\TimeoutException;

/**
 * Represents the result of a forked process.
 *
 * @template TResult
 *
 * @implements Future<TResult>
 *
 * @internal
 */
final class ForkFuture implements Future
{
    /**
     * The interval to sleep between polling attempts in microseconds.
     * This helps prevent busy-waiting and reduces CPU usage while waiting for child processes.
     */
    private const int SLEEP_INTERVAL_MICROSECONDS = 100_000; // 100ms

    /**
     * The result of the forked process, if any.
     *
     * @var TResult|null
     */
    private mixed $result = null;

    /**
     * Indicates whether the result has been resolved.
     */
    private bool $resolved = false;

    /**
     * Creates a new fork result instance.
     */
    public function __construct(
        private readonly int $pid,
        private readonly IPC $memory,
        private readonly Closure $onWait,
    ) {
        //
    }

    /**
     * Awaits the result of the future.
     *
     * @param  int|null  $timeout  Timeout in milliseconds or null for no timeout.
     * @return TResult|null
     *
     * @throws TimeoutException If the wait exceeds the specified timeout.
     */
    public function await(?int $timeout = null): mixed
    {
        if ($this->resolved) {
            return $this->result;
        }

        $this->waitForCompletion($timeout);

        return $this->result;
    }

    /**
     * Waits for the forked process to finish or terminates it if the timeout is exceeded.
     *
     * @param  int|null  $timeout  Timeout in seconds, or null to wait indefinitely.
     *
     * @throws TimeoutException If the process exceeds the given timeout.
     */
    private function waitForCompletion(?int $timeout): void
    {
        $startTime = microtime(true);

        while (true) {
            $pid = pcntl_waitpid($this->pid, $status, WNOHANG);

            if ($pid === -1) {
                // Error or already handled
                $this->resolved = true;
                $this->result = null;

                return;
            }

            if ($pid > 0) {
                // Process finished
                if (! file_exists($this->memory->path()) || filesize($this->memory->path()) === 0) {
                    $this->resolved = true;
                    $this->result = null;

                    return;
                }

                // Invoke the onWait callback to mark process as completed
                ($this->onWait)($this->pid);

                $this->resolved = true;

                /** @var TResult $result */
                $result = unserialize($this->memory->pop());

                $this->result = $result;

                return;
            }

            if ($timeout !== null && (microtime(true) - $startTime) >= ($timeout / 1000)) {
                // Timeout reached, kill the process and throw exception
                posix_kill($this->pid, SIGKILL);
                pcntl_waitpid($this->pid, $status); // Clean up

                $this->resolved = true;
                $this->result = null;

                throw new TimeoutException("Forked process {$this->pid} timed out after {$timeout} milliseconds.");
            }

            // Sleep to prevent busy waiting
            usleep(self::SLEEP_INTERVAL_MICROSECONDS);
        }
    }
}
