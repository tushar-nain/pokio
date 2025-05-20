<?php

declare(strict_types=1);

use Tests\HedgehogException;

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

test('async with an exception thrown', function (): void {
    expect(function (): void {
        $promise = async(function (): void {
            throw new HedgehogException('Not enough hedgehogs');
        });

        await($promise);
    })->toThrow(HedgehogException::class, 'Not enough hedgehogs');
});

test('async with a caught exception', function (): void {
    $promise = async(function (): void {
        throw new HedgehogException('Not enough hedgehogs');
    }, function (Throwable $e): string {
        expect($e)->toBeInstanceOf(HedgehogException::class);
        expect($e->getMessage())->toEqual('Not enough hedgehogs');

        return 'Hedgehogs';
    });

    $result = await($promise);

    expect($result)->toEqual('Hedgehogs');
});
