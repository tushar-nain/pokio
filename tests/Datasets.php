<?php

declare(strict_types=1);

dataset('runtimes', [
    'sync' => fn () => pokio()->useSync(),
    'fork' => function (): void {
        if (! extension_loaded('pcntl') || ! extension_loaded('posix')) {
            $this->markTestSkipped('The pcntl and posix extensions are required to use the fork runtime.');
        }

        pokio()->useFork();
    },
]);
