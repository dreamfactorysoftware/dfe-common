<?php namespace DreamFactory\Enterprise\Common\Auth;

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
    protected $_userClass = 'DreamFactory\\Enterprise\\Database\\Models\\User';
}
