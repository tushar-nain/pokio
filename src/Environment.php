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

        self::$runtime = new ForkRuntime;
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
     */
    public static function runtime(): Runtime
    {
        if (Kernel::instance()->isOrchestrator() === false) {
            return new SyncRuntime();
        }

        $areExtensionsAvailable = extension_loaded('pcntl') && extension_loaded('posix');

        return self::$runtime ??= $areExtensionsAvailable
            ? new ForkRuntime
            : new SyncRuntime;
    }
}
