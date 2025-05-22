<?php

declare(strict_types=1);

namespace Pokio;

/**
 * @internal
 */
final class Kernel
{
    /**
     * The kernel's singleton.
     */
    private static ?self $instance = null;

    /**
     * The process ID of the orchestrator.
     */
    public function __construct(
        private readonly int $orchestratorPid,
    ) {
        //
    }

    /**
     * The process ID of the orchestrator.
     */
    public static function instance(): self
    {
        return self::$instance ??= new self((int) getmypid());
    }

    /**
     * Whether the current process is the orchestrator.
     */
    public function isOrchestrator(): bool
    {
        return $this->orchestratorPid === getmypid();
    }
}
