<?php
namespace DreamFactory\Enterprise\Common\Contracts;

use DreamFactory\Enterprise\Services\Provisioners\ProvisioningRequest;

/**
 * Something that looks like it can provision resources
 */
interface ResourceProvisioner
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * @param ProvisioningRequest|mixed $request
     * @param array                     $options
     *
     * @return mixed
     */
    public function provision( $request, $options = [] );

    /**
     * @param ProvisioningRequest|mixed $request
     * @param array                     $options
     *
     * @return mixed
     */
    public function deprovision( $request, $options = [] );
}