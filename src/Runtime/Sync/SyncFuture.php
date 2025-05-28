<?php

declare(strict_types=1);

namespace Pokio\Runtime\Sync;

use Closure;
use Pokio\Contracts\Future;
use Pokio\Promise;

/**
 * @internal
 *
 * @template TResult
 *
 * @implements Future<TResult>
 */
final readonly class SyncFuture implements Future
{
    /**
     * Creates a new sync result instance.
     *
     * @param  Closure(): TResult  $callback
     */
    public function __construct(private Closure $callback)
    {
        //
    }

    /**
     * Awaits the result of the future.
     *
     * @param  int|null  $timeout  Timeout in milliseconds (ignored here).
     * @return TResult
     */
    public function await(?int $timeout = null): mixed
    {
        $result = ($this->callback)();

        if ($result instanceof Promise) {
            return await($result);
        }

        return $result;
    }
}
