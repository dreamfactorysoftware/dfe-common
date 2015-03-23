<?php namespace DreamFactory\Enterprise\Common\Http\Controllers\Auth;

use DreamFactory\Enterprise\Common\Http\Controllers\BaseController;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommonPasswordController extends BaseController
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use ResetsPasswords;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a new password controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\Guard          $auth
     * @param  \Illuminate\Contracts\Auth\PasswordBroker $passwords
     */
    public function __construct( Guard $auth, PasswordBroker $passwords )
    {
        $this->auth = $auth;
        $this->passwords = $passwords;

        $this->middleware( 'guest' );
    }

    /** @inheritdoc */
    public function postReset( Request $request )
    {
        $this->validate(
            $request,
            [
                'token'    => 'required',
                'email'    => 'required|email',
                'password' => 'required|confirmed',
            ]
        );

        $credentials = $request->only(
            'email',
            'password',
            'password_confirmation',
            'token'
        );

        $response = $this->passwords->reset(
            $credentials,
            function ( $user, $password )
            {
                $user->password_text = bcrypt( $password );

                $user->save();

                $this->auth->login( $user );
            }
        );

        switch ( $response )
        {
            case PasswordBroker::PASSWORD_RESET:
                return redirect( $this->redirectPath() );

            default:
                return redirect()->back()->withInput( $request->only( 'email' ) )->withErrors( ['email' => trans( $response )] );
        }
    }

    /** @inheritdoc */
    public function getEmail()
    {
        return view( 'dfe-common::auth.password' );
    }

    /** @inheritdoc */
    public function getReset( $token = null )
    {
        if ( is_null( $token ) )
        {
            throw new NotFoundHttpException;
        }

        return view( 'dfe-common::auth.reset' )->with( 'token', $token );
    }

}
