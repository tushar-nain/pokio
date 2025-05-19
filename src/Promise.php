<?php

declare(strict_types=1);

namespace Pokio;

use Closure;
use Pokio\Contracts\Result;

final readonly class Promise
{
    private Result $result;

    /**
     * Creates a new promise instance.
     */
    public function __construct(private Closure $callback)
    {
        //
    }

    public function run(): void
    {
        $runtime = Environment::runtime();

        $this->result = $runtime->defer($this->callback);
    }

    /**
     * Resolves the promise.
     */
    public function resolve(): mixed
    {
        return $this->result->get();
    }
}
