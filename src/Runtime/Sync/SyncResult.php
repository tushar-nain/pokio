<?php

declare(strict_types=1);

namespace Pokio\Runtime\Sync;

use Closure;
use Pokio\Contracts\Result;

final readonly class SyncResult implements Result
{
    /**
     * Creates a new sync result instance.
     */
    public function __construct(private Closure $callback)
    {
        //
    }

    /**
     * Resolves the result.
     */
    public function get(): mixed
    {
        return ($this->callback)();
    }
}
