<?php

declare(strict_types=1);

use Pokio\Runtime\Fork\ThrowableCapsule;

test('throwable capsule can serialize to array', function (): void {
    $exception = new RuntimeException('Out of hedgehogs', 42);
    $capsule = new ThrowableCapsule($exception);

    $data = $capsule->__serialize();

    expect($data['message'])->toBe('Out of hedgehogs')
        ->and($data['code'])->toBe(42)
        ->and($data['file'])->toBe($exception->getFile())
        ->and($data['line'])->toBe($exception->getLine())
        ->and($data['trace'])->toBe($exception->getTraceAsString())
        ->and($data['class'])->toBe(RuntimeException::class);
})->with('runtimes');

test('throwable capsule can unserialize from array', function (): void {
    $data = [
        'message' => 'Unicorn overflow',
        'code' => 99,
        'file' => '/tmp/example.php',
        'line' => 1337,
        'trace' => 'fake trace',
        'class' => RuntimeException::class,
    ];

    $capsule = new ThrowableCapsule(new RuntimeException('irrelevant'));

    expect(fn () => $capsule->__unserialize($data))
        ->toThrow(RuntimeException::class, 'Unicorn overflow');
})->with('runtimes');
