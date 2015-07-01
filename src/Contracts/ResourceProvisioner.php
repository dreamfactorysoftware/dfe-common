<?php namespace DreamFactory\Enterprise\Common\Contracts;

use DreamFactory\Enterprise\Common\Provisioners\ProvisioningRequest;

/**
 * Something that looks like it can provision resources
 */
interface ResourceProvisioner
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * Returns the id, config key, or short name, of this provisioner.
     *
     * @return string The id of this provisioner
     */
    public function getProvisionerId();

    /**
     * @param ProvisioningRequest|mixed $request
     * @param array                     $options
     *
     * @return mixed
     */
    public function provision($request, $options = []);

    /**
     * @param ProvisioningRequest|mixed $request
     * @param array                     $options
     *
     * @return mixed
     */
    public function deprovision($request, $options = []);
}