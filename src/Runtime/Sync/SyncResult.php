<?php

declare(strict_types=1);

namespace Pokio\Runtime\Sync;

use Closure;
use Pokio\Contracts\Result;
use Throwable;

final readonly class SyncResult implements Result
{
    /**
     * Creates a new sync result instance.
     */
    public function __construct(private Closure $callback, private ?Closure $rescue = null)
    {
        //
    }

    /**
     * Resolves the result.
     */
    public function get(): mixed
    {
        try {
            return ($this->callback)();
        } catch (Throwable $exception) {
            if ($this->rescue instanceof Closure) {
                return ($this->rescue)($exception);
            }

            throw $exception;
        }
    }
}
