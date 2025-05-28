<?php

declare(strict_types=1);

use Pokio\Exceptions\TimeoutException;
use Pokio\Pokio;
use Pokio\Promise;

if (! function_exists('async')) {
    /**
     * Runs a callback asynchronously and returns a promise.
     *
     * @template TReturn
     *
     * @param  Closure(): TReturn  $callback
     * @return Promise<TReturn>
     */
    function async(Closure $callback): Promise
    {
        return new Promise($callback);
    }
}
if (! function_exists('await')) {
    /**
     * Awaits the resolution of a promise or an array of promises.
     *
     * @template TReturn
     *
     * @param  array<int, Promise<TReturn>>|Promise<TReturn>  $promises
     * @param  int|null  $timeout  Timeout in milliseconds or null for no timeout
     * @return ($promises is array ? array<int, TReturn> : TReturn)
     *
     * @throws TimeoutException If any promise times out.
     */
    function await(array|Promise $promises, ?int $timeout = null): mixed
    {
        if (! is_array($promises)) {
            $promises->defer();

            return $promises->resolve($timeout);
        }

        foreach ($promises as $promise) {
            $promise->defer();
        }

        return array_map(
            static fn (Promise $promise): mixed => $promise->resolve($timeout),
            $promises
        );
    }
}

if (! function_exists('pokio')) {
    /**
     * Returns the Pokio kernel instance.
     */
    function pokio(): Pokio
    {
        return new Pokio;
    }
}
