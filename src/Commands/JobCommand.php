<?php
namespace DreamFactory\Enterprise\Common\Commands;

use DreamFactory\Enterprise\Common\Traits\HasResults;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A base class for all "job" type commands (non-console)
 */
abstract class JobCommand implements ShouldQueue
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
    protected $output;
    /**
     * @type InputInterface
     */
    protected $input;
    /**
     * @type string The fully qualified handler class name
     */
    protected $handlerClass = null;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Provide a handler class name
     *
     * @throws \DreamFactory\Enterprise\Common\Exceptions\NotImplementedException
     */
    public function getHandler()
    {
        if (!$this->handlerClass) {
            throw new \RuntimeException('No "handler" defined for this command.');
        }

        return $this->handlerClass;
    }

    public function setHandler($handlerClass)
    {
        if (!class_exists($handlerClass, false)) {
            throw new \InvalidArgumentException('The class "' . $handlerClass . '" cannot be found or loaded."');
        }

        $this->handlerClass = $handlerClass;

        return $this;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     *
     * @return $this
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param InputInterface $input
     *
     * @return $this
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;

        return $this;
    }
}
