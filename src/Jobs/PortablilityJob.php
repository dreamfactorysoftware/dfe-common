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
     * @type mixed The target
     */
    protected $to;
    /**
     * @type mixed The source
     */
    protected $from;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a new command instance.
     *
     * @param string $instanceId The instance to provision
     * @param mixed  $to         The target
     * @param mixed  $from       The source
     * @param array  $options    Provisioning options
     */
    public function __construct($instanceId, $to = null, $from = null, $options = [])
    {
        parent::__construct($instanceId, $options);

        $this->to = $to;
        $this->from = $from;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

}
