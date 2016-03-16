<?php namespace DreamFactory\Enterprise\Common\Http\Controllers\Auth;

use DreamFactory\Enterprise\Common\Http\Controllers\BaseController;
use DreamFactory\Enterprise\Database\Models\ServiceUser;
use DreamFactory\Enterprise\Database\Models\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;

class CommonPasswordController extends BaseController
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use ResetsPasswords;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string The password reset link request view
     */
    protected $linkRequestView = 'dfe-common::auth.password';
    /**
     * @type string The password reset view
     */
    protected $resetView = 'dfe-common::auth.reset';
    /**
     * @type string Where to go after a reset
     */
    protected $redirectPath = '/auth/login';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a new password controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword|User|ServiceUser $user
     * @param  string                                                       $password
     */
    protected function resetPassword($user, $password)
    {
        $user->password_text = bcrypt($password);
        $user->save();

        /** @noinspection PhpUndefinedMethodInspection */
        Auth::guard($this->getGuard())->login($user);
    }
}
