<?php

declare(strict_types=1);

namespace Pokio\Runtime\Fork;

use Closure;
use Pokio\Contracts\Future;
use Pokio\Contracts\Runtime;
use Pokio\Promise;
use RuntimeException;
use Throwable;

final readonly class ForkRuntime implements Runtime
{
    /**
     *  Defers the given callback to be executed asynchronously.
     *
     * @template TResult
     *
     * @param  Closure(): TResult  $callback
     * @return Future<TResult>
     */
    public function defer(Closure $callback): Future
    {
        $sharedMemory = IPC::create();

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

            $sharedMemory->put($data);

            exit(0);
        }

        /** @var ForkFuture<TResult> $future */
        $future = new ForkFuture($pid, $sharedMemory);

        return $future;
    }
}
