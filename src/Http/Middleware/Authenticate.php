<?php namespace DreamFactory\Enterprise\Common\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

/**
 * Generic/standard authentication middleware
 * Designed for Laravel 5.1.x authentication
 */
class Authenticate
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
        if ($this->auth->guest()) {
            return $request->ajax() ? response()->json('Unauthorized.', 401) : \Redirect::guest('auth/login');
        }

        return $next($request);
    }
}
