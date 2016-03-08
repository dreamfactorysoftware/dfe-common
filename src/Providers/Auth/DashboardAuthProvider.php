<?php namespace DreamFactory\Enterprise\Common\Providers\Auth;

use DreamFactory\Enterprise\Common\Auth\DashboardUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class DashboardAuthProvider extends ServiceProvider
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function boot()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        Auth::provider('dashboard',
            function() {
                /** @noinspection PhpUndefinedMethodInspection */
                return new DashboardUserProvider($this->app['db']->connection(), $this->app['hash'], 'user_t');
            });
    }

    /** @inheritdoc */
    public function register()
    {
    }
}
