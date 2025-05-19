<?php

require __DIR__.'/vendor/autoload.php';

$start = microtime(true);

echo "in the start\n";

$promiseA = async(function () {
    sleep(2);
    echo "Performing task #1\n";
});

echo "in the middle\n";

$promiseB = async(function () {
    sleep(2);

    echo "Performing task #2\n";
});

echo "before await\n";

await($promiseA);
await($promiseB);

$end = microtime(true);

$duration = $end - $start;
echo "Total duration: {$duration} seconds\n";
