<?php
namespace DreamFactory\Enterprise\Common\Contracts;

/**
 * Something that is aware of provisioners
 */
interface ResourceProvisionerAware
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * Returns an instance of the provisioner $name
     *
     * @param string $name
     *
     * @return ResourceProvisioner
     */
    public function getProvisioner($name = null);

    /**
     * Returns an instance of the storage provisioner $name
     *
     * @param string $name
     *
     * @return ResourceProvisioner
     */
    public function getStorageProvisioner($name = null);

    /**
     * Returns an instance of the db provisioner $name
     *
     * @param string $name
     *
     * @return ResourceProvisioner
     */
    public function getDatabaseProvisioner($name = null);
}