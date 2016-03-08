<?php namespace DreamFactory\Enterprise\Common\Auth;

use Illuminate\Auth\DatabaseUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Base class for DreamFactory Enterprise user providers
 */
abstract class BaseUserProvider extends DatabaseUserProvider
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string The model class for the user
     */
    protected $userClass;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function retrieveByCredentials(array $credentials)
    {
        $_condition = [];
        $_data = [];

        foreach ($credentials as $_key => $_value) {
            if (!str_contains($_key, 'password')) {
                $_realKey = $this->mapKey($_key);
                $_condition[] = $_realKey . ' = :' . $_realKey;
                $_data[':' . $_realKey] = $_value;
            }
        }

        /**
         * Only allow active users to login
         *  0 = not active, 1 = active
         */
        $_condition[] = 'active_ind = :active_ind';
        $_data[':active_ind'] = 1;

        /** @noinspection PhpUndefinedMethodInspection */
        return $this->getProviderModel()->whereRaw(implode(' AND ', $_condition), $_data)->first();
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->getProviderModel()->find($identifier);
    }

    /**
     * Retrieve a user by by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string $token
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        return $this->getProviderModel()->where('id', $identifier)->where('remember_token', $token)->first();
    }

    /**
     * Maps a generic key name to a database column name
     *
     * @param string $key
     *
     * @return string
     */
    protected function mapKey($key)
    {
        switch ($key) {
            case 'password':
                $key = 'password_text';
                break;

            case 'email':
                $key = 'email_addr_text';
                break;

            case 'remember':
                $key = 'remember_token';
                break;
        }

        return $key;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array                                      $credentials
     *
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $this->hasher->check($credentials['password'], $user->getAuthPassword());
    }

    /**
     * Returns a new instance of the user class
     *
     * @return mixed
     */
    protected function getProviderModel()
    {
        return new $this->userClass;
    }
}
