<?php namespace DreamFactory\Enterprise\Common\Jobs;

/**
 * A base class for all DFE portability requests
 */
abstract class PortabilityJob extends BaseInstanceJob
{
    //******************************************************************************
    //* Members
    //******************************************************************************


    /**
     * @type mixed The job target
     */
    protected $target;
    /**
     * @type mixed Where to send the output
     */
    protected $outputFile;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a new command instance.
     *
     * @param string     $instanceId The instance to provision
     * @param mixed|null $target     The target
     * @param mixed|null $outputFile Where output goes
     * @param array      $options    Provisioning options
     */
    public function __construct($instanceId, $target = null, $outputFile = null, $options = [])
    {
        parent::__construct($instanceId, $options);

        $target && $this->target = $target;
        $outputFile && $this->outputFile = $outputFile;
    }

    /**
     * @param mixed $target
     *
     * @return $this
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return mixed
     */
    public function getOutputFile()
    {
        return $this->outputFile;
    }

    /**
     * @param mixed $outputFile
     *
     * @return $this
     */
    public function setOutputFile($outputFile)
    {
        $this->outputFile = $outputFile;

        return $this;
    }
}
