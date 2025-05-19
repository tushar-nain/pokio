<?php

use Pokio\Environment;

pest()->beforeEach(function (): void {
    match ($_ENV['POKIO_RUNTIME'] ?? null) {
        'sync' => Environment::useSync(),
        'fork' => Environment::useFork(),
        default => null,
    };
});
