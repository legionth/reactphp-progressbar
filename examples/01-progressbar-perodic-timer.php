<?php

use Legionth\React\ProgressBar\ProgressbarStream;
use React\EventLoop\Factory;

require_once __DIR__ . '/../vendor/autoload.php';

$loop = Factory::create();

$progressBarStream = new ProgressbarStream();

$input = new React\Stream\WritableResourceStream(STDOUT, $loop);
$output = new React\Stream\ReadableResourceStream(STDIN, $loop);

$output->pipe($progressBarStream);

$progressBarStream->on('error', function ($errorMessage) {
    echo $errorMessage;
});

$progressBarStream->on('data', function ($progressBarString) use ($input) {
    $input->write($progressBarString . PHP_EOL);
});

$loop->addPeriodicTimer(1, function () use ($output) {
    $output->emit('data', array(1));
});

$loop->addPeriodicTimer(3, function () use ($output) {
    $output->emit('data', array(10));
});

$loop->run();