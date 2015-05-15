<?php
namespace DreamFactory\Enterprise\Common\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * A base class for DFE service providers
 */
abstract class BaseServiceProvider extends ServiceProvider
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string The name of the alias to create
     */
    const ALIAS_NAME = false;
    /**
     * @type string The name of the service in the IoC
     */
    const IOC_NAME = false;
    /**
     * @type string The name of the manager service in the IoC
     */
    const MANAGER_IOC_NAME = false;

    //********************************************************************************
    //* Public Methods
    //********************************************************************************

    /**
     * Register a shared binding in the container.
     *
     * @param  string               $abstract
     * @param  \Closure|string|null $concrete
     *
     * @return void
     */
    public function singleton( $abstract, $concrete )
    {
        //  Register object into instance container
        $this->app->singleton( $abstract ?: static::IOC_NAME, $concrete );
    }

    /**
     * Register a binding with the container.
     *
     * @param  string|array         $abstract
     * @param  \Closure|string|null $concrete
     * @param  bool                 $shared
     *
     * @return void
     */
    public function bind( $abstract, $concrete, $shared = false )
    {
        //  Register object into instance container
        $this->app->bind( $abstract ?: static::IOC_NAME, $concrete, $shared );
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [static::IOC_NAME];
    }

    /**
     * @return string Returns this provider's IoC name
     */
    public function __invoke()
    {
        return static::IOC_NAME ?: null;
    }
}
