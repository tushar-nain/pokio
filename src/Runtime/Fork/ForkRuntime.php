<?php

declare(strict_types=1);

namespace Pokio\Runtime\Fork;

use Closure;
use Pokio\Contracts\Result;
use Pokio\Contracts\Runtime;
use RuntimeException;

final readonly class ForkRuntime implements Runtime
{
    /**
     * Defers the given callback to be executed asynchronously.
     */
    public function defer(Closure $callback): Result
    {
        // random 27-bit positive key
        $shmKey = random_int(0x100000, 0x7FFFFFFF);

        $pid = pcntl_fork();

        if ($pid === -1) {
            throw new RuntimeException('Failed to fork process');
        }

        if ($pid === 0) {
            $result = $callback();

            $data = serialize($result);

            $shmId = shmop_open($shmKey, 'c', 0600, strlen($data));

            if (! $shmId) {
                throw new RuntimeException('Failed to create shared memory block');
            }

            shmop_write($shmId, $data, 0);

            exit(0);
        }

        return new ForkResult($pid, $shmKey);
    }
}
