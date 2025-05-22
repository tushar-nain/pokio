<?php

declare(strict_types=1);

namespace Pokio\Runtime\Fork;

use RuntimeException;

/**
 * @internal
 */
final readonly class IPC
{
    /**
     * Creates a new memory block.
     */
    private function __construct(
        private int $address,
    ) {
        //
    }

    /**
     * Creates an inter-process communication (IPC) memory block.
     */
    public static function create(): self
    {
        return new self(
            random_int(0x100000, 0x7FFFFFFF),
        );
    }

    /**
     * Reads the contents of the memory block.
     */
    public function put(string $data): void
    {
        $block = shmop_open($this->address, 'c', 0600, mb_strlen($data));

        if ($block === false) {
            throw new RuntimeException('Failed to create shared memory block');
        }

        shmop_write($block, $data, 0);
    }

    /**
     * Pops the contents of the memory block and clears it.
     */
    public function pop(): string
    {
        $block = shmop_open($this->address, 'a', 0, 0);

        if ($block === false) {
            throw new RuntimeException('Failed to open shared memory block');
        }

        $data = shmop_read($block, 0, shmop_size($block));

        shmop_delete($block);

        return $data;
    }
}
