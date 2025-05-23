<?php

declare(strict_types=1);

use Pokio\Runtime\Fork\ThrowableCapsule;
use Tests\Fixtures\Exceptions\HedgehogException;

test('ThrowableCapsule serializes correctly', function (): void {
    $exception = new HedgehogException('Out of hedgehogs', 42);
    $capsule = new ThrowableCapsule($exception);

    $data = $capsule->__serialize();

    expect($data['message'])->toBe('Out of hedgehogs')
        ->and($data['code'])->toBe(42)
        ->and($data['file'])->toBe($exception->getFile())
        ->and($data['line'])->toBe($exception->getLine())
        ->and($data['trace'])->toBe($exception->getTraceAsString())
        ->and($data['class'])->toBe(HedgehogException::class);
})->with('runtimes');

test('ThrowableCapsule unserializes and throws original exception type', function (): void {
    $data = [
        'message' => 'Unicorn overflow',
        'code' => 99,
        'file' => '/tmp/example.php',
        'line' => 1337,
        'trace' => 'fake trace',
        'class' => HedgehogException::class,
    ];

    $capsule = new ThrowableCapsule(new HedgehogException('irrelevant'));

    expect(fn () => $capsule->__unserialize($data))
        ->toThrow(HedgehogException::class, 'Unicorn overflow');
})->with('runtimes');
