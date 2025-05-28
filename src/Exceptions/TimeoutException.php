<?php

declare(strict_types=1);

namespace Pokio\Exceptions;

use RuntimeException;

/**
 * Exception thrown when a forked process times out.
 */
final class TimeoutException extends RuntimeException {}
