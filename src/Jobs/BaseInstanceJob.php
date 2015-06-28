<?php
namespace DreamFactory\Enterprise\Common\Jobs;

use DreamFactory\Enterprise\Database\Models\Instance;

/**
 * A base class for all DFE instance-related "job" commands (non-console)
 */
abstract class BaseInstanceJob extends BaseJob
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type Instance
     */
    protected $instanceId;
    /**
     * @type array
     */
    protected $options = [];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a new command instance.
     *
     * @param string $instanceId The instance to provision
     * @param array  $options    Provisioning options
     */
    public function __construct($instanceId, $options = [])
    {
        $this->instanceId = $instanceId;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getInstanceId()
    {
        return $this->instanceId;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
