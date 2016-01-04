<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Common\Provisioners\BaseResponse;

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
    protected $startTime = 0;
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
        $this->elapsedTime = 0;
        $this->startTime = microtime(true);

        return $this;
    }

    /**
     * Times a closure. Any arguments passed after the closure become arguments to the closure.
     * Elapsed time stored in $this->elapsedTime.
     *
     * @param \Closure $closure   The closure to profile
     * @param array    $arguments Optional arguments for closure
     *
     * @return array [0=elapsed time, 1=response]
     */
    public function profile(\Closure $closure, array $arguments = [])
    {
        $this->elapsedTime = 0;
        $this->startTime = microtime(true);

        try {
            $_response = call_user_func_array($closure, $arguments);
        } catch (\Exception $_ex) {
            \Log::error($_ex->getMessage());
            $_response = null;
        }
        finally {
            $_elapsed = $this->stopTimer();
        }

        $_response && ($_response instanceof BaseResponse) && $_response->setElapsedTime($_elapsed);

        return [$_elapsed, $_response];
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
        if (!empty($this->startTime)) {
            $this->elapsedTime = (microtime(true) - $this->startTime);
            //\Log::info('stopTimer: elapsed=' . $this->elapsedTime . ' start=' . $this->startTime);
        }

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
     * Returns the time elapsed in microseconds since the startTime, or the current $this->elapsedTime if set.
     *
     * @return float
     */
    public function getElapsedTime()
    {
        return 0 === $this->elapsedTime ? microtime(true) - $this->startTime : $this->elapsedTime;
    }
}
