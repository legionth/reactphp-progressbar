<?php


class ProgressbarStreamTest extends TestCase
{
    public function testValidValue()
    {
        $progressbarStream = new \Legionth\React\ProgressBar\ProgressbarStream();
        $input = new \React\Stream\ThroughStream();

        $input->pipe($progressbarStream);

        $result = '';

        $progressbarStream->on('data', function ($data) use (&$result) {
            $result = $data;
        });

        $currentValueResult = null;
        $progressbarStream->on('currentValue', function ($data) use (&$currentValueResult) {
            $currentValueResult = $data;
        });

        $maximumValueResult = null;
        $progressbarStream->on('maximumValue', function ($data) use (&$maximumValueResult) {
            $maximumValueResult = $data;
        });

        $input->emit('data', array(5));

        $expectedValue = '|█████...............................................................................................|5%';

        $this->assertEquals($expectedValue, $result);
        $this->assertEquals(5, $currentValueResult);
        $this->assertEquals(100, $maximumValueResult);

    }


    public function testInvalidValue()
    {
        $progressbarStream = new \Legionth\React\ProgressBar\ProgressbarStream();
        $input = new \React\Stream\ThroughStream();

        $errorResult = false;

        $progressbarStream->on('error', function () use (&$errorResult) {
            $errorResult = true;
        });

        $closeResult = false;
        $progressbarStream->on('close', function () use (&$closeResult) {
            $closeResult = true;
        });

        $input->pipe($progressbarStream);

        $input->emit('data', array('invalid'));

        $this->assertTrue($errorResult);
        $this->assertTrue($closeResult);

    }

    public function testStreamCanNotBeBiggerThanMaximum()
    {
        $progressbarStream = new \Legionth\React\ProgressBar\ProgressbarStream();
        $input = new \React\Stream\ThroughStream();

        $dataResult = '';
        $result = false;

        $progressbarStream->on('data', function ($data) use (&$dataResult) {
            $dataResult = $data;
        });

        $progressbarStream->on('end', function () use (&$result) {
            $result = true;
        });

        $input->pipe($progressbarStream);

        $input->emit('data', array(101));

        $expectedValue = '|████████████████████████████████████████████████████████████████████████████████████████████████████|100%';

        $this->assertTrue($result);
        $this->assertEquals($expectedValue,$dataResult);
    }

    public function testCustomProgressBar()
    {
        $progressbarStream = new \Legionth\React\ProgressBar\ProgressbarStream(
            'x',
            'z',
                        'o',
            5,
            200,
            '(',
            ')'
        );
        $input = new \React\Stream\ThroughStream();

        $input->pipe($progressbarStream);

        $result = '';

        $progressbarStream->on('data', function ($data) use (&$result) {
            $result = $data;
        });

        $currentValueResult = null;
        $progressbarStream->on('currentValue', function ($data) use (&$currentValueResult) {
            $currentValueResult = $data;
        });

        $maximumValueResult = null;
        $progressbarStream->on('maximumValue', function ($data) use (&$maximumValueResult) {
            $maximumValueResult = $data;
        });

        $input->emit('data', array(5));

        $expectedValue = '(xxxxxxxxxxzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz)10o';

        $this->assertEquals($expectedValue, $result);
        $this->assertEquals(10, $currentValueResult);
        $this->assertEquals(200, $maximumValueResult);
    }
}