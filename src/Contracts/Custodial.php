<?php namespace DreamFactory\Enterprise\Common\Contracts;

/**
 * Something that maintains logs of tasks performed, like a time card
 */
interface Custodial
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string The key in an archive containing the custodial information
     */
    const CUSTODY_KEY = '_custodial';
    /**
     * @type string The key in an archive containing the activity logs
     */
    const CUSTODY_LOG_KEY = '_custodial.activity';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Adds an entry to the specified custodial "log" for this object
     *
     * @param string     $activity A "name" denoting the activity performed. ie. "import" or "export"
     * @param array|null $extras   Any extra data to log along with the activity
     *
     * @return $this
     */
    public function addActivity($activity, array $extras = null);

    /**
     * Get the logged activity
     *
     * @return array
     */
    public function getActivities();

    /**
     * @param string $where The key in the manifest to place the custody logs
     * @param bool   $flush if true, any cached entries are cleared
     *
     * @return $this
     */
    public function addCustodyLogs($where, $flush = false);
}