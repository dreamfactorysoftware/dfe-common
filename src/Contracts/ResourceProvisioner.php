<?php namespace DreamFactory\Enterprise\Common\Contracts;

use DreamFactory\Enterprise\Common\Provisioners\BaseResponse;
use DreamFactory\Enterprise\Common\Provisioners\ProvisionServiceRequest;
use DreamFactory\Enterprise\Common\Provisioners\ProvisionServiceResponse;

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
     * @param ProvisionServiceRequest|mixed $request
     *
     * @return ProvisionServiceResponse|mixed
     */
    public function provision($request);

    /**
     * @param ProvisionServiceRequest|mixed $request
     *
     * @return ProvisionServiceResponse|mixed
     */
    public function deprovision($request);

    /**
     * Returns the overall response to the request once handled
     *
     * @return BaseResponse
     */
    public function getResponse();
}