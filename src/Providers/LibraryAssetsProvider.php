<?php namespace DreamFactory\Enterprise\Common\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class LibraryAssetsProvider extends BaseServiceProvider
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string Our container name
     */
    const IOC_NAME = 'dfe-common';
    /**
     * @type string Our alias name
     */
    const ALIAS_NAME = 'Common';
    /**
     * @type string Relative path to config file
     */
    const CONFIG_NAME = 'dfe-common.php';
    /**
     * @type string Relative path of asset installation
     */
    const ASSET_PUBLISH_PATH = '/vendor/dfe-common';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function boot()
    {
        $_libBase = realpath( __DIR__ . '/../../' );
        $_configPath = $_libBase . '/config';
        $_resourcesPath = $_libBase . '/resources';

        //  Views
        $this->loadViewsFrom( $_resourcesPath . '/views', static::IOC_NAME );

        //  Config
        $this->publishes( [$_configPath . '/' . static::CONFIG_NAME => config_path( static::CONFIG_NAME ),] );

        //  And assets...
        $this->publishes( [$_resourcesPath . '/assets' => public_path( static::ASSET_PUBLISH_PATH )], 'public' );

        Log::debug( 'dfe-common library assets booted.' );
    }

    /** @inheritdoc */
    public function register()
    {
        View::addNamespace( static::IOC_NAME, __DIR__ . '/../../resources/views' );
    }

    public static function compiles()
    {
        return array_merge(
            parent::compiles(),
            [
                __DIR__ . DIRECTORY_SEPARATOR . __FILE__,
            ]
        );
    }

}
