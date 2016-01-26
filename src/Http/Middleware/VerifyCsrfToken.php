<?php namespace DreamFactory\Enterprise\Common\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;

/**
 * Generic/standard authentication middleware
 * Designed for Laravel 5.1.x authentication
 */
class VerifyCsrfToken extends BaseVerifier
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $_ex) {
            //  Catch expired sessions
            return Redirect::guest('auth/login')->withErrors([
                'Session Expired' => Lang::get('dashboard.session-expired',
                    'Your session has expired or is otherwise not valid.'),
            ]);
        }
    }
}
