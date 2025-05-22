<?php

declare(strict_types=1);

use Tests\Fixtures\Exceptions\HedgehogException;

test('async with an exception thrown', function (): void {
    expect(function (): void {
        $promise = async(function (): void {
            throw new HedgehogException('Not enough hedgehogs');
        });

        await($promise);
    })->toThrow(HedgehogException::class, 'Not enough hedgehogs');
})->with('runtimes');
