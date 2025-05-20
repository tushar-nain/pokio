<?php

namespace Pokio\Runtime\Sync;

use Closure;
use Pokio\Contracts\Result;
use Pokio\Contracts\Runtime;

final readonly class SyncRuntime implements Runtime
{
    /**
     * Defers the given callback to be executed asynchronously.
     */
    public function defer(Closure $callback, ?Closure $rescue = null): Result
    {
        return new SyncResult($callback, $rescue);
    }
}
