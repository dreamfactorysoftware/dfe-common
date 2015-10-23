<?php namespace DreamFactory\Enterprise\Common\Providers\Auth;

use DreamFactory\Enterprise\Common\Auth\ConsoleUserProvider;
use Illuminate\Support\ServiceProvider;

class ConsoleAuthProvider extends ServiceProvider
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function boot()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->app['auth']->extend('console',
            function (){
                /** @noinspection PhpUndefinedMethodInspection */
                return new ConsoleUserProvider($this->app['db']->connection(), $this->app['hash'], 'service_user_t');
            });
    }

    /** @inheritdoc */
    public function register()
    {
    }
}
