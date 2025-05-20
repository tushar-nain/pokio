<?php

declare(strict_types=1);

namespace Pokio\Runtime\Fork;

use Closure;
use Pokio\Contracts\Result;
use Pokio\Contracts\Runtime;
use Pokio\Support\PipePath;
use RuntimeException;

final readonly class ForkRuntime implements Runtime
{
    /**
     * Defers the given callback to be executed asynchronously.
     */
    public function defer(Closure $callback): Result
    {
        $pipePath = PipePath::get();

        if (file_exists($pipePath)) {
            unlink($pipePath);
        }

        if (! posix_mkfifo($pipePath, 0600)) {
            throw new RuntimeException('Failed to create pipe');
        }

        $pid = pcntl_fork();

        if ($pid === -1) {
            throw new RuntimeException('Failed to fork process');
        }

        if ($pid === 0) {
            $result = $callback();
            $pipe = fopen($pipePath, 'w');

            if ($pipe === false) {
                throw new RuntimeException('Failed to open pipe (writing)');
            }

            fwrite($pipe, serialize($result));
            fclose($pipe);

            exit(0);
        }

        return new ForkResult($pipePath);
    }
}
