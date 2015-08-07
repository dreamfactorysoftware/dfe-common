<?php
namespace DreamFactory\Enterprise\Common\Jobs;

use DreamFactory\Enterprise\Common\Contracts\InstanceAware;
use DreamFactory\Enterprise\Database\Models\Instance;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

        parent::__construct(array_get($options, 'cluster-id'),
            array_get($options, 'server-id'),
            array_get($options, 'tag'));
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
        static $_instance;

        if (!$_instance && $this->instanceId) {
            try {
                $_instance = $this->_findInstance($this->instanceId);
            } catch (ModelNotFoundException $_ex) {
                //  ignored
            }
        }

        return $_instance;
    }
}
