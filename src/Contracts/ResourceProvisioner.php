<?php
namespace DreamFactory\Enterprise\Common\Contracts;

/**
 * Something that looks like it can provision resources
 */
interface ResourceProvisioner
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * @param \DreamFactory\Enterprise\Services\Provisioners\ProvisioningRequest|mixed $request
     * @param array                                                                    $options
     *
     * @return mixed
     */
    public function provision( $request, $options = [] );

    /**
     * @param \DreamFactory\Enterprise\Services\Provisioners\ProvisioningRequest|mixed $request
     * @param array                                                                    $options
     *
     * @return mixed
     */
    public function deprovision( $request, $options = [] );
}