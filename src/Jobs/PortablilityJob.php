<?php
namespace DreamFactory\Enterprise\Common\Jobs;

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

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a new command instance.
     *
     * @param string $instanceId The instance to provision
     * @param mixed  $target     The target
     * @param array  $options    Provisioning options
     */
    public function __construct($instanceId, $target = null, $options = [])
    {
        parent::__construct($instanceId, $options);

        $this->target = $target;
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

}
