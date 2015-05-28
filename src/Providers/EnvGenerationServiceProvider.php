<?php namespace DreamFactory\Enterprise\Common\Providers;

use DreamFactory\Enterprise\Common\Services\ScalpelService;
use Illuminate\Contracts\Foundation\Application;

/**
 * Register the env generation service
 *
 * This service creates a ".env.cluster.json" file suitable for use with deployed instances in a DFE installation
 * your the "providers" array in your config/app.php file:
 */
class ScalpelServiceProvider extends BaseServiceProvider
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /** @inheritdoc */
    const IOC_NAME = 'scalpel';

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
        //  Register object into instance container
        $this->singleton(
            static::IOC_NAME,
            function ( Application $app )
            {
                return new EnvGenerationService( $app );
            }
        );
    }
}
