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
        $this->startTime = microtime(true);
        $this->elapsedTime = 0;

        //\Log::info('startTimer: start=' . $this->startTime);

        return $this;
    }

    /**
     * Times a closure. Any arguments passed after the closure become arguments to the closure.
     * Elapsed time stored in $this->elapsedTime.
     *
     * @param \Closure $closure The closure to profile
     * @param [mixed]  $option1 Optional closure parameter
     * @param [mixed]  $option2 Optional closure parameter
     *
     * @return mixed the return value of the closure
     */
    public function profile(\Closure $closure)
    {
        array_shift($_arguments = func_get_args());

        $this->startTimer();
        $_result = call_user_func_array($closure, empty($_arguments) ? [] : $_arguments);
        $this->stopTimer();

        return $_result;
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
