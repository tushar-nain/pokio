<?php

namespace Pokio\Runtime\Fork;

use Pokio\Contracts\Result;
use RuntimeException;

/**
 * Represents the result of a forked process.
 */
final class ForkResult implements Result
{
    /**
     * The result of the forked process, if any.
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
        private readonly int $shmKey,
    ) {
        //
    }

    /**
     * The result of the asynchronous operation.
     */
    public function get(): mixed
    {
        if ($this->resolved) {
            return $this->result;
        }

        // wait for child process to exit
        pcntl_waitpid($this->pid, $status);

        $shmId = shmop_open($this->shmKey, 'a', 0, 0);

        if (! $shmId) {
            throw new RuntimeException('Failed to open shared memory block');
        }

        $data = shmop_read($shmId, 0, shmop_size($shmId));

        shmop_delete($shmId);

        $this->resolved = true;

        return $this->result = unserialize($data);
    }
}
