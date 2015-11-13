<?php namespace DreamFactory\Enterprise\Common\Traits;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * A trait that adds logging methods to a class that fulfill the LoggerInterface and LoggerAwareInterface contracts.
 * Has indentation and prefix capabilities as well
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
    /**
     * @type int The current indent level
     */
    protected $indent = 0;
    /**
     * @type int The number of spaces per indent level
     */
    protected $indentSize = 4;
    /**
     * @type string The marker to increment the indent level
     */
    protected $indentStartMarker = '>>>';
    /**
     * @type string The marker to drop an indent level
     */
    protected $indentStopMarker = '<<<';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger ?: $this->logger = Log::getMonolog();
    }

    /**
     * @param int $level
     * @param string|array $message
     * @param array $context
     *
     * @return bool
     */
    public function log($level, $message, array $context = [])
    {
        Log::log($level, $message, $context);
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     *
     * @return bool
     */
    public function emergency($message, array $context = [])
    {
        return Log::emergency($message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     *
     * @return bool
     */
    public function alert($message, array $context = [])
    {
        return Log::alert($message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     *
     * @return bool
     */
    public function critical($message, array $context = [])
    {
        return Log::critical($message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     *
     * @return bool
     */
    public function error($message, array $context = [])
    {
        return Log::error($message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     *
     * @return bool
     */
    public function warning($message, array $context = [])
    {
        return Log::warning($message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     *
     * @return bool
     */
    public function notice($message, array $context = [])
    {
        return Log::notice($message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     *
     * @return bool
     */
    public function info($message, array $context = [])
    {
        return Log::info($message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     *
     * @return bool
     */
    public function debug($message, array $context = [])
    {
        return Log::debug($message, $context);
    }

    /**
     * Initializes the lumberjack logging faculties
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param string|null $prefix
     *
     * @return $this
     */
    protected function initializeLumberjack(LoggerInterface $logger, $prefix = null)
    {
        $logger && $this->setLogger($logger);
        $prefix && $this->setLumberjackPrefix($prefix);

        return $this;
    }

    /**
     * @param string|array $message
     * @param bool $addPrefix If true, the message(s) will be prefixed
     *
     * @return array|false|string
     */
    protected function formatMessage($message, $addPrefix = true)
    {
        $_messages = [];
        $_wasArray = true;

        if (!is_array($message)) {
            $message = [$message];
            $_wasArray = false;
        }

        $_startLength = strlen($this->indentStartMarker);
        $_stopLength = strlen($this->indentStopMarker);

        //  Prepare the prefix for potential prepending!
        $addPrefix = false;

        $_prefix = (empty($this->lumberjackPrefix) || !$addPrefix) ? null : $this->lumberjackPrefix . ' ';

        foreach ($message as $_message) {
            $_indentAfter = false;

            if ($this->indentStartMarker == substr($_message, 0, $_startLength)) {
                $_indentAfter = true;
            } elseif ($this->indentStopMarker == substr($_message, 0, $_stopLength)) {
                $this->indent--;
            }

            $_messages[] =
                $_prefix .
                str_pad(' ', ($this->indent * $this->indentSize) - 1) .
                trim(str_replace([$this->indentStartMarker, $this->indentStopMarker], null, $_message));

            //  Indent after so the first line doesn't get indented
            $_indentAfter && $this->indent++;
        }

        return $_wasArray ? $_messages : reset($_messages);
    }

    /**
     * @return string
     */
    protected function getLumberjackPrefix()
    {
        return $this->lumberjackPrefix;
    }

    /**
     * @param string $lumberjackPrefix
     * @param bool $brackets If true, prefix is ensconced in lovely square brackets with a space on top.
     *
     * @return $this
     */
    protected function setLumberjackPrefix($lumberjackPrefix, $brackets = true)
    {
        $this->lumberjackPrefix = trim($lumberjackPrefix, '[]');
        ($brackets && !empty($this->lumberjackPrefix)) &&
        $this->lumberjackPrefix = '[' . $this->lumberjackPrefix . '] ';

        return $this;
    }
}
