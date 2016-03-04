<?php namespace DreamFactory\Enterprise\Common\Contracts;

use DreamFactory\Enterprise\Common\Provisioners\BaseRequest;
use DreamFactory\Enterprise\Common\Provisioners\BaseResponse;

/**
 * A service that provides virtual provisioning capabilities
 */
interface VirtualProvisioner
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * Generates an APP_KEY suitable for use with Laravel deployments
     *
     * @return string The key
     */
    public function makeAppKey();

    /**
     * Returns the id, config key, or short name, of this provisioner.
     *
     * @return string The id of this provisioner
     */
    public function getProvisionerId();

    /**
     * @param BaseRequest|\DreamFactory\Enterprise\Services\Provisioners\ProvisionServiceRequest|mixed $request
     *
     * @return BaseResponse|\DreamFactory\Enterprise\Services\Provisioners\ProvisionServiceResponse|mixed
     */
    public function provision($request);

    /**
     * @param BaseRequest|\DreamFactory\Enterprise\Services\Provisioners\ProvisionServiceRequest|mixed $request
     *
     * @return BaseResponse|\DreamFactory\Enterprise\Services\Provisioners\ProvisionServiceResponse|mixed
     */
    public function deprovision($request);
}
