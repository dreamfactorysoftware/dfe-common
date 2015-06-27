<?php namespace DreamFactory\Enterprise\Common\Contracts;

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
     * @param \DreamFactory\Enterprise\Services\Provisioners\ProvisioningRequest|mixed $request
     * @param array                                                                    $options
     *
     * @return mixed
     */
    public function provision($request, $options = []);

    /**
     * @param \DreamFactory\Enterprise\Services\Provisioners\ProvisioningRequest|mixed $request
     * @param array                                                                    $options
     *
     * @return mixed
     */
    public function deprovision($request, $options = []);
}