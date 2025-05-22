<?php

declare(strict_types=1);

namespace Pokio\Runtime\Fork;

use Pokio\Contracts\Future;

/**
 * Represents the result of a forked process.
 *
 * @template TResult
 *
 * @implements Future<TResult>
 *
 * @internal
 */
final class ForkFuture implements Future
{
    /**
     * The result of the forked process, if any.
     *
     * @var TResult
     */
    private mixed $result = null;

    /**
     * Indicates whether the result has been resolved.
     */
    private bool $resolved = false;

    /**
     * Creates a new fork result instance.
     */
    public function __construct(
        private readonly int $pid,
        private readonly IPC $memory,
    ) {
        //
    }

    /**
     * Awaits the result of the future.
     *
     * @return TResult
     */
    public function await(): mixed
    {
        if ($this->resolved) {
            return $this->result;
        }

        pcntl_waitpid($this->pid, $status);

        $this->resolved = true;

        /** @var TResult $result */
        $result = unserialize($this->memory->pop());

        return $this->result = $result;
    }
}
