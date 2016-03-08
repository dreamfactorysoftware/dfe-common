<?php namespace DreamFactory\Enterprise\Common\Auth;

/**
 * Provides users for dashboard users
 */
class DashboardUserProvider extends BaseUserProvider
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /** @inheritdoc */
    protected $userClass = 'DreamFactory\Enterprise\Database\Models\User';
}
