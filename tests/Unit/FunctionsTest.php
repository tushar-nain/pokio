<?php

test('async with a single promise', function (): void {
    $promise = async(fn (): int => 1 + 2);

    $result = await($promise);

    expect($result)->toBe(3);
});

test('async with a multiple promises', function (): void {
    $promiseA = async(fn (): int => 1 + 2);

    $promiseB = async(fn (): int => 3 + 4);

    [$resultA, $resultB] = await([$promiseA, $promiseB]);

    expect($resultA)->toBe(3)
        ->and($resultB)->toBe(7);
});
