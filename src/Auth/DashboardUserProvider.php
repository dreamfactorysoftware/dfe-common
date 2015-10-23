<?php namespace DreamFactory\Enterprise\Common\Auth;

use DreamFactory\Enterprise\Database\Models\User;

/**
 * Provides users for dashboard users
 */
class DashboardUserProvider extends BaseUserProvider
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string Our user class
     */
    protected $userClass = User::class;
}
