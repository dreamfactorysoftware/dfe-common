<?php namespace DreamFactory\Enterprise\Common\Providers;

use Illuminate\Contracts\Foundation\Application;
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
     * Called after construction
     */
    public function boot()
    {
        //  Does nothing but encourages calling the parent method
    }

    /**
     * Register a shared binding in the container.
     *
     * @param  string               $abstract
     * @param  \Closure|string|null $concrete
     *
     * @return void
     */
    public function singleton($abstract, $concrete)
    {
        //  Register object into instance container
        $this->app->singleton($abstract ?: static::IOC_NAME, $concrete);
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
    public function bind($abstract, $concrete, $shared = false)
    {
        //  Register object into instance container
        $this->app->bind($abstract ?: static::IOC_NAME, $concrete, $shared);
    }

    /**
     * @return array
     */
    public function provides()
    {
        return array_merge(parent::provides(), [static::IOC_NAME,]);
    }

    /**
     * Returns the service configuration either based on class name or argument name. Override method to provide custom configurations
     *
     * @param string|null $name
     * @param array       $default
     *
     * @return array
     */
    public static function getServiceConfig($name = null, $default = [])
    {
        if (null === ($_name = $name)) {
            $_mirror = new \ReflectionClass(get_called_class());
            $_name = snake_case(str_ireplace('ServiceProvider', null, $_mirror->getShortName()));
            unset($_mirror);
        }

        return config($_name, $default);
    }

    /**
     * @return string Returns this provider's IoC name
     */
    public function __invoke()
    {
        return static::IOC_NAME ?: null;
    }

    /**
     * @param Application|null $app
     *
     * @return static
     */
    public static function service(Application $app = null)
    {
        $app = $app ?: app();

        return $app->make(static::IOC_NAME);
    }
}
