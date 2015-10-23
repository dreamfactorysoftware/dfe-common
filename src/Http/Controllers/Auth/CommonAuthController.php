<?php namespace DreamFactory\Enterprise\Common\Http\Controllers\Auth;

use DreamFactory\Enterprise\Common\Http\Controllers\BaseController;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;

abstract class CommonAuthController extends BaseController
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a new authentication controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /** @inheritdoc */
    public function getLogin()
    {
        return view('dfe-common::auth.login');
    }

    /** @inheritdoc */
    public function getRegister()
    {
        return view(config('auth.open-registration', false) ? 'dfe-common::auth.register'
            : 'dfe-common::auth.no-register');
    }

    /** @inheritdoc */
    abstract public function validator(array $data);

    /** @inheritdoc */
    abstract public function create(array $data);
}
