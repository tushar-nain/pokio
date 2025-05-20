<?php

declare(strict_types=1);

use Pokio\Environment;

dataset('runtimes', [
    'sync' => fn () => Environment::useSync(),
    'fork' => fn () => Environment::useFork(),
]);
