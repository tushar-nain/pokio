<?php

declare(strict_types=1);

use Pokio\Environment;

pest()->beforeEach(function (): void {
    match ($_ENV['POKIO_RUNTIME'] ?? null) {
        'sync' => Environment::useSync(),
        'fork' => Environment::useFork(),
        default => null,
    };
});

if (! function_exists('ensureForkEnvironment')) {
    /**
     * Ensures the current environment is set to fork.
     * Skips the test if the pcntl and posix extensions are not loaded.
     */
    function ensureForkEnvironment(): void
    {
        if (! extension_loaded('pcntl') || ! extension_loaded('posix')) {
            test()->markTestSkipped('The pcntl and posix extensions are required to use the fork runtime.');
        }

        pokio()->useFork();
    }
}

if (! function_exists('ensureSyncEnvironment')) {
    /**
     * Ensures the current environment is set to sync.
     */
    function ensureSyncEnvironment(): void
    {
        pokio()->useSync();
    }
}
