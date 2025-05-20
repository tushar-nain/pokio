<?php

namespace Pokio\Contracts;

use Closure;

interface Runtime
{
    /**
     * Defers the given callback to be executed asynchronously.
     */
    public function defer(Closure $callback, ?Closure $rescue = null): Result;
}
