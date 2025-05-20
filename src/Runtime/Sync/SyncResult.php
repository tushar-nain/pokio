<?php

declare(strict_types=1);

namespace Pokio\Runtime\Sync;

use Closure;
use Pokio\Contracts\Result;
use Pokio\Promise;
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
            $result = ($this->callback)();

            if ($result instanceof Promise) {
                $result = await($result);
            }

            return $result;
        } catch (Throwable $exception) {
            if ($this->rescue instanceof Closure) {
                return ($this->rescue)($exception);
            }

            throw $exception;
        }
    }
}
