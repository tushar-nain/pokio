<?php

declare(strict_types=1);

arch()->preset()->php();

arch()->preset()->strict()->ignoring([
    'usleep',
]);

arch()->preset()->security()->ignoring([
    'serialize',
    'unserialize',
    'shell_exec',
]);

arch('base')
    ->expect('Pokio')
    ->toUseStrictEquality()
    ->toHavePropertiesDocumented()
    ->toHaveMethodsDocumented();
