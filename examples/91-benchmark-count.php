<?php

// To run the example, execute the following command:
// $ php examples/91-benchmark-count.php < examples/users.tsv
//
// If you want to achieve more convincing result,
// you can run the same example with some larger TSV files.
// Take a look at:
// @link https://datasets.imdbws.com/

use React\EventLoop\Loop;

require __DIR__ . '/../vendor/autoload.php';

if (extension_loaded('xdebug')) {
    echo 'NOTICE: The "xdebug" extension is loaded, this has a major impact on performance.' . PHP_EOL;
}

$decoder = new Clue\React\Tsv\TsvDecoder(new React\Stream\ReadableResourceStream(STDIN));

$count = 0;
$decoder->on('data', function () use (&$count) {
    ++$count;
});

$start = microtime(true);
$report = Loop::addPeriodicTimer(0.05, function () use (&$count, $start) {
    printf("\r%d records in %0.3fs...", $count, microtime(true) - $start);
});

$decoder->on('close', function () use (&$count, $report, $start) {
    $now = microtime(true);
    Loop::cancelTimer($report);

    printf("\r%d records in %0.3fs => %d records/s\n", $count, $now - $start, $count / ($now - $start));
});
