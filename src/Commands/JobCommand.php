<?php
namespace DreamFactory\Enterprise\Common\Commands;

use DreamFactory\Enterprise\Common\Traits\HasResults;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A base class for all "job" type commands (non-console)
 */
abstract class JobCommand implements ShouldBeQueued
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string|bool The queue upon which to push myself. Set to false to not use queuing
     */
    const JOB_QUEUE = false;

    //******************************************************************************
    //* Traits
    //******************************************************************************

    use InteractsWithQueue, SerializesModels, HasResults;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type OutputInterface
     */
    protected $_output;
    /**
     * @type InputInterface
     */
    protected $_input;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Provide a handler class name
     *
     * @throws \DreamFactory\Enterprise\Common\Exceptions\NotImplementedException
     */
    abstract public function getHandler();

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->_output;
    }

    /**
     * @param OutputInterface $output
     *
     * @return $this
     */
    public function setOutput( OutputInterface $output )
    {
        $this->_output = $output;

        return $this;
    }

    /**
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->_input;
    }

    /**
     * @param InputInterface $input
     *
     * @return $this
     */
    public function setInput( InputInterface $input )
    {
        $this->_input = $input;

        return $this;
    }
}
