<?php

declare(strict_types=1);

use Tests\Fixtures\Exceptions\HedgehogException;

test('async with a single promise', function (): void {
    $promise = async(fn (): int => 1 + 2);

    $result = await($promise);

    expect($result)->toBe(3);
})->with('runtimes');

test('async with a multiple promises', function (): void {
    $promiseA = async(fn (): int => 1 + 2);

    $promiseB = async(fn (): int => 3 + 4);

    [$resultA, $resultB] = await([$promiseA, $promiseB]);

    expect($resultA)->toBe(3)
        ->and($resultB)->toBe(7);
})->with('runtimes');

test('async with single then callback', function (): void {
    $promise = async(fn (): int => 1 + 2)
        ->then(fn (int $result): int => $result * 2);

    $result = await($promise);

    expect($result)->toBe(6);
})->with('runtimes');

test('async with multiple then callbacks', function (): void {
    $promise = async(fn (): int => 1 + 2)
        ->then(fn (int $result): int => $result * 2)
        ->then(fn (int $result): int => $result - 1);

    $result = await($promise);

    expect($result)->toBe(5);
})->with('runtimes');

test('async with a catch callback', function (): void {
    $caught = false;

    $promise = async(function (): void {
        throw new HedgehogException('Exception 1');
    })->catch(function (Throwable $th) use (&$caught): void {
        expect($th)->toBeInstanceOf(HedgehogException::class)
            ->and($th->getMessage())->toBe('Exception 1');

        $caught = true;
    });

    await($promise);

    expect($caught)->toBeTrue();

})->with('runtimes');

test('async with a finally callback', function (): void {
    $called = false;

    $promise = async(fn (): int => 1 + 2)
        ->finally(function () use (&$called): void {
            $called = true;
        });

    $result = await($promise);

    expect($result)->toBe(3)->and($called)->toBeTrue();
})->with('runtimes');

test('async with a catch callback that throws an exception', function (): void {
    $promise = async(function (): void {
        throw new HedgehogException('Exception 1');
    })->catch(function (Throwable $th): void {
        throw new HedgehogException('Exception 2');
    });

    expect(function () use ($promise): void {
        await($promise);
    })->toThrow(HedgehogException::class, 'Exception 2');
})->with('runtimes');
