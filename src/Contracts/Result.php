<?php

namespace Pokio\Contracts;

interface Result
{
    /**
     * The result of the asynchronous operation.
     */
    public function get(): mixed;
}
