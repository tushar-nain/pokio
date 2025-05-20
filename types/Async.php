<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

$promise = async(fn () => 1);
assertType('int', await($promise));

$promiseA = async(fn () => 'string');
$promiseB = async(fn () => 'string');
assertType('array<int, string>', await([$promiseA, $promiseB]));
