<?php

// To run the example, execute the following command:
// $ php examples/tsv2ndjson.php < examples/users.tsv

use React\EventLoop\Loop;

require __DIR__ . '/../vendor/autoload.php';

$exit = 0;
$in = new React\Stream\ReadableResourceStream(STDIN);
$out = new React\Stream\WritableResourceStream(STDOUT);
$info = new React\Stream\WritableResourceStream(STDERR);

$formatter = new React\Stream\ThroughStream(function ($data) {
    $data = array_filter($data, function ($value) {
        return ($value !== '');
    });

    return json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_FORCE_OBJECT) . PHP_EOL;
});

$decoder = new Clue\React\Tsv\TsvDecoder($in);
$decoder->pipe($formatter)->pipe($out);

$decoder->on('error', function (Exception $e) use ($info, &$exit) {
    $info->write('ERROR: ' . $e->getMessage() . PHP_EOL);
    $exit = 1;
});

$info->write('You can pipe/write a valid TSV stream to STDIN' . PHP_EOL);
$info->write('Valid NDJSON (Newline-Delimited JSON) will be forwarded to STDOUT' . PHP_EOL);
$info->write('Invalid TSV will raise an error on STDERR and exit with code 1' . PHP_EOL);

Loop::run();

exit($exit);
