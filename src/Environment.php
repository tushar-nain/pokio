<?php

declare(strict_types=1);

namespace Pokio;

use RuntimeException;

/**
 * @internal
 */
final class Environment
{
    /**
     * The number of processes that can be run in parallel.
     */
    public static function maxProcesses(): int
    {
        $cpuCores = (int) shell_exec('getconf _NPROCESSORS_ONLN');
        if ($cpuCores <= 0) {
            $cpuCores = 1;
        }

        $ioFactor = (int) getenv('FORK_IO_FACTOR') ?: 3;
        $maxByCpu = $cpuCores * $ioFactor;

        $os = PHP_OS_FAMILY;
        if ($os === 'Linux') {
            $memInfo = @file_get_contents('/proc/meminfo');
            // @phpstan-ignore-next-line
            if (! preg_match('/MemTotal:\s+(\d+) kB/', $memInfo, $matches)) {
                throw new RuntimeException('Unable to determine total memory on Linux');
            }
            $totalMemory = (int) $matches[1] * 1024;
        } elseif ($os === 'Darwin') {
            $totalMemory = (int) shell_exec('sysctl -n hw.memsize');
        } else {
            throw new RuntimeException("Unsupported OS: $os");
        }

        $perProcessMemory = (int) getenv('FORK_MEM_PER_PROC') ?: 100 * 1024 * 1024; // 100MB
        $maxByMemory = intdiv($totalMemory, $perProcessMemory);

        $maxProcesses = min($maxByCpu, $maxByMemory);

        return max(1, $maxProcesses);
    }

    /**
     * Whether the current environment supports forking.
     */
    public static function supportsFork(): bool
    {
        return extension_loaded('pcntl') && extension_loaded('posix');
    }
}
