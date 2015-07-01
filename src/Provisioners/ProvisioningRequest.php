<?php namespace DreamFactory\Enterprise\Common\Provisioners;

use DreamFactory\Enterprise\Common\Contracts\PrivatePathAware;
use DreamFactory\Enterprise\Common\Contracts\ResourceProvisioner;
use DreamFactory\Enterprise\Database\Models\Instance;
use League\Flysystem\Filesystem;

class ProvisioningRequest
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type Instance The instance to provision
     */
    protected $instance;
    /**
     * @type Filesystem The instance's local file system
     */
    protected $storage;
    /**
     * @type bool True if this is a DE-provision
     */
    protected $deprovision = false;
    /**
     * @type bool True if the $request should be forced
     */
    protected $forced = false;
    /**
     * @type ResourceProvisioner|PrivatePathAware
     */
    protected $storageProvisioner;
    /**
     * @type mixed The result of provisioning
     */
    protected $result;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param Filesystem                                        $storage
     * @param bool                                              $deprovision
     * @param bool                                              $force
     */
    public function __construct(Instance $instance, Filesystem $storage = null, $deprovision = false, $force = false)
    {
        $this->instance = $instance;
        $this->storage = $storage;
        $this->deprovision = $deprovision;
        $this->forced = $force;
    }

    /**
     * @return Instance
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param bool $createIfNull
     *
     * @return Filesystem
     */
    public function getStorage($createIfNull = true)
    {
        //  Use requested file system if one...
        if (null === $this->storage && $createIfNull) {
            $this->setStorage(
                $_storage = $this->getInstance()->getRootStorageMount()
            );
        }

        return $this->storage;
    }

    /**
     * @return boolean
     */
    public function isDeprovision()
    {
        return $this->deprovision;
    }

    /**
     * @param \League\Flysystem\Filesystem $storage
     *
     * @return ProvisioningRequest
     */
    public function setStorage(Filesystem $storage)
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isForced()
    {
        return $this->forced;
    }

    /**
     * @param boolean $forced
     *
     * @return ProvisioningRequest
     */
    public function setForced($forced)
    {
        $this->forced = $forced;

        return $this;
    }

    /**
     * @return ResourceProvisioner|PrivatePathAware
     */
    public function getStorageProvisioner()
    {
        return $this->storageProvisioner;
    }

    /**
     * @param ResourceProvisioner $storageProvisioner
     *
     * @return ProvisioningRequest
     */
    public function setStorageProvisioner($storageProvisioner)
    {
        $this->storageProvisioner = $storageProvisioner;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     *
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }
}
