<?php namespace DreamFactory\Enterprise\Common\Traits;

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
    public function addActivity($activity, $extras = null)
    {
        !isset($this->custodianActivity) && ($this->custodianActivity = []);
        !isset($this->custodianActivity[$activity]) && ($this->custodianActivity[$activity] = []);

        $this->custodianActivity[$activity][] =
            array_merge(
                $extras,
                ['time' => microtime(true), 'timestamp' => date('c')]
            );

        return $this;
    }

    /** @inheritdoc */
    public function getActivities()
    {
        return $this->custodianActivity;
    }

    /** @inheritdoc */
    public function addCustodyLogs($where, $flush = false)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->set($where, $this->getActivities());

        $flush && ($this->custodianActivity = []);

        return $this;
    }

}
