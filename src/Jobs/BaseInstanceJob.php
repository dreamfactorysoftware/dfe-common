<?php
namespace DreamFactory\Enterprise\Common\Jobs;

use DreamFactory\Enterprise\Common\Contracts\InstanceAware;
use DreamFactory\Enterprise\Database\Models\Instance;

/**
 * A base class for all DFE instance-related "job" commands (non-console)
 */
abstract class BaseInstanceJob extends BaseEnterpriseJob implements InstanceAware
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
    protected $options;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a new command instance.
     *
     * @param string|int $instanceId The instance to provision
     * @param array      $options    Provisioning options
     */
    public function __construct($instanceId, array $options = [])
    {
        $this->instanceId = $instanceId;
        $this->options = $options;

        parent::__construct(array_get($options, 'cluster-id'), array_get($options, 'server-id'));
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

    /**
     * @return \DreamFactory\Enterprise\Database\Models\Instance|null
     */
    public function getInstance()
    {
        return $this->instanceId ? $this->_findInstance($this->instanceId) : null;
    }
}
