<?php

declare(strict_types=1);

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
    function async(Closure $callback, ?Closure $rescue = null): Promise
    {
        $promise = new Promise($callback, $rescue);

        $promise->run();

        return $promise;
    }
}
if (! function_exists('await')) {
    /**
     * Awaits the resolution of a promise.
     *
     * @template TReturn
     *
     * @param  array<int, Promise<TReturn>>|Promise<TReturn>  $promises
     * @return ($promises is array ? array<int, TReturn> : TReturn)
     */
    function await(array|Promise $promises): mixed
    {
        if (! is_array($promises)) {
            return $promises->resolve();
        }

        return array_map(
            static fn (Promise $promise): mixed => $promise->resolve(),
            $promises
        );
    }
}
