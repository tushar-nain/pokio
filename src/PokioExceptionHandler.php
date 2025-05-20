<?php

declare(strict_types=1);

namespace Pokio;

use ReflectionClass;
use ReflectionException;
use Throwable;

final class PokioExceptionHandler
{
    public function __construct(public Throwable $exception)
    {
        //
    }

    public function __serialize(): array
    {
        return [
            'message' => $this->exception->getMessage(),
            'code' => $this->exception->getCode(),
            'file' => $this->exception->getFile(),
            'line' => $this->exception->getLine(),
            'trace' => $this->exception->getTraceAsString(),
            'class' => $this->exception::class,
        ];
    }

    public function __unserialize(array $data)
    {
        $exception = new $data['class']($data['message'], $data['code']);

        try {
            $reflection = new ReflectionClass($exception);

            $fileProp = $reflection->getProperty('file');
            $fileProp->setAccessible(true);
            $fileProp->setValue($exception, $data['file']);

            $lineProp = $reflection->getProperty('line');
            $lineProp->setAccessible(true);
            $lineProp->setValue($exception, $data['line']);
        } catch (ReflectionException) {
            // Skip if properties can't be changed
        }

        throw $exception;
    }
}
