<?php namespace DreamFactory\Enterprise\Common\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;

/**
 * Generic/standard authentication middleware
 * Designed for Laravel 5.1.x authentication
 */
class RedirectIfAuthenticated
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * The Guard implementation.
     *
     * @type Guard
     */
    protected $auth;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth
     *
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->check()) {
            return new RedirectResponse(url('/'));
        }

        return $next($request);
    }

}
