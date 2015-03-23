<?php namespace DreamFactory\Enterprise\Common\Providers;

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
    const CONFIG_NAME = '/config/dfe-common.php';
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

        //  Views
        $this->loadViewsFrom( $_libBase . '/resources/views', static::IOC_NAME );
        $this->publishes( [$_libBase . '/resources/views' => base_path( 'resources/views/vendor/' . static::IOC_NAME )] );

        //  Config
        $this->publishes( [$_libBase . static::CONFIG_NAME => config_path( static::CONFIG_NAME ),] );

        //  And assets...
        $this->publishes( [$_libBase . '/resources/assets' => public_path( static::ASSET_PUBLISH_PATH )], 'public' );
    }

    /** @inheritdoc */
    public function register()
    {
    }

}
