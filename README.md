# ProgressbarStream

An event-driven progressbar stream that can be added to other streams
to show its current progress.

**Table of Contents**
 * [Usage](#usage)
   * [data-Event](#data-event)
   * [currentValue-Event](#currentvalue-event)
   * [maximumValue-Event](#maximumvalue-event)
 * [Installation](#installation)
 
## Usage

This stream can be integrate like any other
[ReactPHP Stream](https://github.com/reactphp/stream/)

```php
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
```

This example will update the progressbar every 1 second by 1 unit/percent and
every 3 seconds by 10 units/percent.
This example can be found in the [examples folder](/examples).

The progressbar stream will never overflow the maximum value.
The stream will end if the current value reaches or goes beyond the maximum
value.

### data-Event

The `data` event contains the visualization of the progress bar.
The data emitted by this event is a string.
This event will be emitted if the progressbar is updated.

### currentValue-Event

The `currentValue` event contains the integer value of the current progressbar.
This event will be emitted if the progressbar is updated.

### maximumValue-Event

The `maximumValue` event contains the integer value of the maximum reachable value
of the progressbar.
This event will be emitted if the progressbar is updated.

### Customize

The constructor

## Installation

The recommended way to install this library is [through Composer](https://getcomposer.org).
[New to Composer?](https://getcomposer.org/doc/00-intro.md)

This will install the latest supported version:

```bash
$ composer require legionth/progressbar:^1.0
```