<?php namespace DreamFactory\Enterprise\Common\Traits;

/**
 * A trait that keeps track of things checked in and out
 */
trait Custodian /** @implements \DreamFactory\Enterprise\Common\Contracts\Custodial */
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function addActivity($activity, $extras = null)
    {
        $_log = $this->get(static::CUSTODY_LOG_KEY, []);

        !is_array($_log) && $_log = [$_log];
        !isset($_log[$activity]) && $_log[$activity] = [];
        $_log[$activity][] = array_merge($extras, ['time' => microtime(true), 'timestamp' => date('c')]);

        $this->put(static::CUSTODY_LOG_KEY, $_log);

        return $this;
    }

    /** @inheritdoc */
    public function getActivities()
    {
        return $this->get(static::CUSTODY_LOG_KEY, []);
    }
}
