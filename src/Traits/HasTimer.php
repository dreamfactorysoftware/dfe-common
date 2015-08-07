<?php namespace DreamFactory\Enterprise\Common\Traits;

/**
 * A trait for things that need a timer
 */
trait HasTimer
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type double
     */
    protected $startTime = 0.0;
    /**
     * @type double
     */
    protected $elapsedTime;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return $this
     */
    public function startTimer()
    {
        $this->startTime = microtime(true);

        return $this;
    }

    /**
     * Stops time and sets elapsedTime
     *
     * @param bool $returnElapsed If true, the elapsed time is returned.
     *
     * @return float|$this
     */
    public function stopTimer($returnElapsed = true)
    {
        $this->elapsedTime = microtime(true) - $this->startTime;
        $this->startTime = 0;

        return $returnElapsed ? $this->elapsedTime : $this;
    }

    /**
     * @return float
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param float $elapsedTime
     *
     * @return $this
     */
    public function setElapsedTime($elapsedTime)
    {
        $this->elapsedTime = $elapsedTime;

        return $this;
    }

    /**
     * @return float
     */
    public function getElapsedTime()
    {
        return $this->elapsedTime ?: $this->elapsedTime = microtime(true) - $this->startTime;
    }
}