<?php

declare(strict_types=1);

namespace Pokio\Contracts;

use Pokio\Exceptions\TimeoutException;

/**
 * @internal
 *
 * @template TResult
 */
interface Future
{
    /**
     * Awaits the result of the asynchronous operation.
     *
     * @param  int|null  $timeout  Timeout in milliseconds or null for no timeout.
     * @return TResult
     *
     * @throws TimeoutException If the operation times out.
     */
    public function await(?int $timeout = null): mixed;
}
