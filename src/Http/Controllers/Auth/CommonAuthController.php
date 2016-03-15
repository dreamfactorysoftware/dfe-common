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
    //* Members
    //******************************************************************************

    /**
     * @type string The login view
     */
    protected $loginView = 'dfe-common::auth.login';
    /**
     * @type string The register view
     */
    protected $registerView = 'dfe-common::auth.register';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a new authentication controller instance.
     */
    public function __construct()
    {
        //  Show the "closed" screen if registration is disabled
        !config('auth.open-registration', false) && $this->registerView = 'dfe-common::auth.no-register';

        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /** @inheritdoc */
    abstract public function validator(array $data);

    /** @inheritdoc */
    abstract public function create(array $data);
}
