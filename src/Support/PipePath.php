<?php

declare(strict_types=1);

namespace Pokio\Support;

final readonly class PipePath
{
    /**
     * Get the path to the pipe.
     */
    public static function get(): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            sys_get_temp_dir(),
            'pokio_pipe_'.uniqid(),
        ]);
    }
}
