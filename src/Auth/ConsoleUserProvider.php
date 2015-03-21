<?php namespace DreamFactory\Enterprise\Common\Auth;

/**
 * Provides users for the console logins
 */
class ConsoleUserProvider extends BaseUserProvider
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    protected $_userClass = 'DreamFactory\\Library\\Fabric\\Database\\Models\\Deploy\\ServiceUser';
}
