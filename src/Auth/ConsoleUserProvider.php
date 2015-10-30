<?php namespace DreamFactory\Enterprise\Common\Auth;

use DreamFactory\Enterprise\Database\Models\ServiceUser;

/**
 * Provides users for the console logins
 */
class ConsoleUserProvider extends BaseUserProvider
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    protected $userClass = ServiceUser::class;
}
