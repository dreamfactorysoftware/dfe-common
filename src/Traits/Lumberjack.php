<?php namespace DreamFactory\Enterprise\Common\Traits;

use Psr\Log\LoggerAwareTrait;

/**
 * A trait that adds complete logging functionality
 */
trait Lumberjack
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use LoggerAwareTrait;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function emergency( $message, array $context = array() )
    {
        \Log::emergency( $message, $context );
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function alert( $message, array $context = array() )
    {
        \Log::alert( $message, $context );
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function critical( $message, array $context = array() )
    {
        \Log::critical( $message, $context );
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function error( $message, array $context = array() )
    {
        \Log::error( $message, $context );
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function warning( $message, array $context = array() )
    {
        \Log::warning( $message, $context );
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function notice( $message, array $context = array() )
    {
        \Log::notice( $message, $context );
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function info( $message, array $context = array() )
    {
        \Log::info( $message, $context );
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function debug( $message, array $context = array() )
    {
        \Log::debug( $message, $context );
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function log( $level, $message, array $context = array() )
    {
        \Log::log( $level, $message, $context );

        return $this;
    }
}
