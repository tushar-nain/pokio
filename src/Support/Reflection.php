<?php

declare(strict_types=1);

namespace Pokio\Support;

use Closure;
use ReflectionFunction;
use ReflectionNamedType;
use Throwable;

/**
 * @internal
 */
final readonly class Reflection
{
    /**
     * Checks if the given callback is catchable.
     *
     * @param  Closure(Throwable): mixed  $callback
     */
    public static function isCatchable(Closure $callback, Throwable $throwable): bool
    {
        $reflection = new ReflectionFunction($callback);
        $parameters = $reflection->getParameters();
        $types = [];

        if (count($parameters) > 0) {
            $type = $parameters[0]->getType();

            /** @phpstan-ignore-next-line */
            $types = is_array($type) ? $type : [$type];
        }

        $matchesType = false;
        /** @var array<int, ReflectionNamedType> $types */
        $types = array_filter($types);

        foreach ($types as $type) {
            $matchesType = $type->getName() === get_debug_type($throwable)
                || is_a($throwable, $type->getName());

            if ($matchesType) {
                break;
            }
        }

        return count($types) === 0 || $matchesType;
    }
}
