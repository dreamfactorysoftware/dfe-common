<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Common\Contracts\Custodial;

/**
 * A trait that keeps track of things checked in and out
 */
trait Custodian /** @implements \DreamFactory\Enterprise\Common\Contracts\Custodial */
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type array Data to write to manifest
     */
    private $custodianActivity = [];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function addActivity($activity, array $extras = [])
    {
        !isset($this->custodianActivity) && ($this->custodianActivity = []);

        $this->custodianActivity[] = [
            $activity => array_merge($extras, ['timestamp' => date('c')]),
        ];

        return $this;
    }

    /** @inheritdoc */
    public function getActivities()
    {
        return $this->custodianActivity;
    }

    /** @inheritdoc */
    public function addCustodyLogs($where = Custodial::CUSTODY_LOG_KEY, $flush = false)
    {
        $_activities = $this->getActivities();

        /** @noinspection PhpUndefinedMethodInspection */
        $this->set($where, $_activities);
        $flush && ($this->custodianActivity = []);

        return $this;
    }

}
