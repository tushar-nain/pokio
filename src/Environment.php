<?php

declare(strict_types=1);

namespace Pokio;

use Pokio\Contracts\Runtime;
use Pokio\Runtime\Fork\ForkRuntime;
use Pokio\Runtime\Sync\SyncRuntime;
use RuntimeException;

/**
 * @internal
 */
final class Environment
{
    /**
     * The environment's runtime.
     */
    public static ?Runtime $runtime = null;

    /**
     * The environment's runtime.
     */
    public static function useFork(): void
    {
        if (! extension_loaded('pcntl') || ! extension_loaded('posix')) {
            throw new RuntimeException('The pcntl and posix extensions are required to use the fork runtime.');
        }

        self::$runtime = new ForkRuntime(self::maxProcesses());
    }

    /**
     * The environment's runtime.
     */
    public static function useSync(): void
    {
        self::$runtime = new SyncRuntime;
    }

    /**
     * Resolves the environment's runtime.
     *
     * @internal
     */
    public static function runtime(): Runtime
    {
        if (Kernel::instance()->isOrchestrator() === false) {
            return new SyncRuntime();
        }

        $areExtensionsAvailable = extension_loaded('pcntl') && extension_loaded('posix');

        return self::$runtime ??= $areExtensionsAvailable
            ? new ForkRuntime(self::maxProcesses())
            : new SyncRuntime;
    }

    /**
     * The number of processes that can be run in parallel.
     */
    private static function maxProcesses(): int
    {
        $cpuCores = (int) shell_exec('nproc');
        $ioFactor = (int) getenv('FORK_IO_FACTOR') ?: 3;
        $maxByCpu = $cpuCores * $ioFactor;

        $os = PHP_OS_FAMILY;
        if ($os === 'Linux') {
            $memInfo = file_get_contents('/proc/meminfo');
            if ($memInfo === false || ! preg_match('/MemTotal:\s+(\d+) kB/', $memInfo, $matches)) {
                throw new RuntimeException('Unable to determine total memory on Linux');
            }
            $totalMemory = (int) $matches[1] * 1024;
        } elseif ($os === 'Darwin') {
            // macOS: get memory using sysctl
            $totalMemory = (int) shell_exec('sysctl -n hw.memsize');
        } else {
            throw new RuntimeException("Unsupported OS: $os");
        }

        $perProcessMemory = (int) getenv('FORK_MEM_PER_PROC') ?: 100 * 1024 * 1024; // 100MB
        $maxByMemory = intdiv($totalMemory, $perProcessMemory);

        $maxProcesses = min($maxByCpu, $maxByMemory);

        return max(1, $maxProcesses);
    }
}
