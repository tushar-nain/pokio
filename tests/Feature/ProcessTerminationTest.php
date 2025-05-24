<?php

declare(strict_types=1);

test('fork: die inside async closure terminates subprocess, not parent', function () {
    ensureForkEnvironment();

    $promise = async(function () {
        exit('Goodbye!');
    });

    $result = await($promise);
    expect($result)->toBeNull();
});

test('fork: exit inside async closure terminates subprocess, not parent', function () {
    ensureForkEnvironment();

    $promise = async(function () {
        exit(42);
    });

    $result = await($promise);
    expect($result)->toBeNull();
});

test('fork: async process gets killed, does not affect parent', function () {
    ensureForkEnvironment();

    $promise = async(function () {
        posix_kill(posix_getpid(), SIGKILL);

        return 'This will not be returned';
    });

    $result = await($promise);
    expect($result)->toBeNull();
});
