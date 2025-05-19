<?php

test('async with a single promise', function () {
    $promise = async(function () {
        return 1 + 2;
    });

    $result = await($promise);

    expect($result)->toBe(3);
});

test('async with a multiple promises', function () {
    $promiseA = async(function () {
        return 1 + 2;
    });

    $promiseB = async(function () {
        return 3 + 4;
    });

    [$resultA, $resultB] = await([$promiseA, $promiseB]);

    expect($resultA)->toBe(3)
        ->and($resultB)->toBe(7);
});
