<?php

namespace Legionth\React\ProgressBar;


use React\Stream\Util;

/**
 * Class ProgressbarStream
 * @package Legionth\React\ProgressBar
 */
class ProgressbarStream
    extends \Evenement\EventEmitter
    implements \React\Stream\DuplexStreamInterface
{
    /** @var bool  */
    private $closed = false;

    /** @var int  */
    private $maximumValue;

    /** @var string  */
    private $progressbarCharacter;

    /** @var int  */
    private $currentValue;

    /** @var string  */
    private $unitCharacter;

    /** @var string */
    private $startBarCharacter;

    /** @var string */
    private $endBarCharacter;

    /** @var string */
    private $fillerCharacter;

    /**
     * @param string $progressbarCharacter
     * @param string $fillerCharacter
     * @param string $unitCharacter
     * @param int $currentValue
     * @param int $maximumValue
     * @param string $startBarCharacter
     * @param string $endBarCharacter
     */
    public function __construct(
        $progressbarCharacter = '█',
        $fillerCharacter = '.',
        $unitCharacter = '%',
        $currentValue = 0,
        $maximumValue = 100,
        $startBarCharacter = '|',
        $endBarCharacter = '|'
    ) {
        $this->progressbarCharacter = $progressbarCharacter;
        $this->fillerCharacter = $fillerCharacter;
        $this->unitCharacter = $unitCharacter;
        $this->currentValue = $currentValue;
        $this->maximumValue = $maximumValue;
        $this->startBarCharacter = $startBarCharacter;
        $this->endBarCharacter = $endBarCharacter;
    }

    /**
     * @inheritDoc
     */
    public function isReadable()
    {
        return ! $this->closed;
    }

    /**
     * @inheritDoc
     */
    public function pause()
    {
        return;
    }

    /**
     * @inheritDoc
     */
    public function resume()
    {
        return;
    }

    /**
     * @inheritDoc
     */
    public function pipe(\React\Stream\WritableStreamInterface $dest, array $options = array())
    {
        Util::pipe($this, $dest, $options);
        return $dest;
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
        if (true === $this->closed) {
            return;
        }

        $this->closed = true;

        $this->emit('close');
    }

    /**
     * @inheritDoc
     */
    public function isWritable()
    {
        return ! $this->closed;
    }

    /**
     * @inheritDoc
     */
    public function write($data)
    {
        $integerValue = (int) $data;
        if ((string) $integerValue !== (string) $data) {
            $this->emit('error', array('Only integer values are allowed'));
            return $this->close();
        }
        $this->currentValue += (int) $data;

        $difference = $this->maximumValue - $this->currentValue;
        if ($difference <= 0) {
            $this->currentValue = $this->maximumValue;

            $progressBar = str_repeat($this->progressbarCharacter, $this->maximumValue);
            $this->emitProgressBar($progressBar);

            return $this->end();
        }

        $progressBar = str_repeat($this->progressbarCharacter, $this->currentValue);
        $progressBar .= str_repeat($this->fillerCharacter, $difference);

        $this->emitProgressBar($progressBar);
    }

    /**
     * @inheritDoc
     */
    public function end($data = null)
    {
        $this->close();
        $this->emit('end');
    }

    private function emitProgressBar($progressBar)
    {
        $dataString = $this->startBarCharacter;
        $dataString .= $progressBar;
        $dataString .= $this->endBarCharacter;
        $dataString .= $this->currentValue;
        $dataString .= $this->unitCharacter;

        $this->emit('data', array($dataString));
        $this->emit('currentValue', array($this->currentValue));
        $this->emit('maximumValue', array($this->maximumValue));
    }
}