<?php namespace DreamFactory\Enterprise\Common\Providers;

use DreamFactory\Enterprise\Common\Services\InstanceStorageService;
use DreamFactory\Enterprise\Database\Enums\GuestLocations;

/**
 * Registers the instance storage service for the default guest location
 */
class InstanceStorageServiceProvider extends BaseServiceProvider
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /** @inheritdoc */
    const IOC_NAME = 'dfe.instance-storage';

    //********************************************************************************
    //* Public Methods
    //********************************************************************************

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //  Register the manager
        $this->singleton(
            static::IOC_NAME,
            function ($app){
                return new InstanceStorageService($app,
                    config('provisioning.default-guest-location', GuestLocations::DFE_CLUSTER));
            }
        );
    }

}
