<?php namespace DreamFactory\Enterprise\Common\Traits;

use Monolog\Logger;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * A trait that adds complete logging functionality and fulfills the
 * LoggerInterface and LoggerAwareInterface contracts
 */
trait Lumberjack
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use LoggerAwareTrait;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string The prefix for log entries, if any.
     */
    protected $lumberjackPrefix;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return string
     */
    public function getLumberjackPrefix()
    {
        return $this->lumberjackPrefix;
    }

    /**
     * @param string $lumberjackPrefix
     *
     * @return $this
     */
    public function setLumberjackPrefix($lumberjackPrefix)
    {
        $this->lumberjackPrefix = '[' . trim($lumberjackPrefix, '[]') . '] ';

        return $this;
    }

    /**
     * @param int          $level
     * @param string|array $message
     * @param array        $context
     *
     * @return bool
     */
    public function log($level, $message, array $context = [])
    {
        return !$this->logger ? false : $this->logger->log($level, $this->prefixLogEntry($message), $context);
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return bool
     */
    public function emergency($message, array $context = [])
    {
        return $this->log(Logger::EMERGENCY, $message, $context);
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
     * @return bool
     */
    public function alert($message, array $context = [])
    {
        return $this->log(Logger::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return bool
     */
    public function critical($message, array $context = [])
    {
        return $this->log(Logger::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return bool
     */
    public function error($message, array $context = [])
    {
        return $this->log(Logger::ERROR, $message, $context);
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
     * @return bool
     */
    public function warning($message, array $context = [])
    {
        return $this->log(Logger::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return bool
     */
    public function notice($message, array $context = [])
    {
        return $this->log(Logger::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return bool
     */
    public function info($message, array $context = [])
    {
        return $this->log(Logger::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return bool
     */
    public function debug($message, array $context = [])
    {
        return $this->log(Logger::DEBUG, $message, $context);
    }

    /**
     * Prefixes log messages with the middleware stamp
     *
     * @param string|array $message
     *
     * @return array|string
     */
    protected function prefixLogEntry($message)
    {
        if (empty($this->lumberjackPrefix)) {
            return $message;
        }

        $this->lumberjackPrefix = trim($this->lumberjackPrefix);

        $_messages = [];
        $_array = true;

        if (!is_array($message)) {
            $message = [$message];
            $_array = false;
        }

        foreach ($message as $_message) {
            $_messages[] = $this->lumberjackPrefix . ' ' . $_message;
        }

        return $_array ? $_messages : current($_messages);
    }

    /**
     * Initializes the lumberjack logging faculties
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param string|null              $prefix
     */
    protected function initializeLumberjack(LoggerInterface $logger, $prefix = null)
    {
        $logger && !$this->logger && $this->setLogger($logger);

        if (!$this->lumberjackPrefix && $prefix) {
            $this->setLumberjackPrefix($prefix);
        }
    }
}