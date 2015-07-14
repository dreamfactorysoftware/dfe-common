<?php namespace DreamFactory\Enterprise\Common\Listeners;

use DreamFactory\Enterprise\Common\Traits\EntityLookup;
use DreamFactory\Enterprise\Common\Traits\Lumberjack;
use Psr\Log\LoggerInterface;

/**
 * A base class for listeners. Includes entity lookup and logging
 */
class BaseListener
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string The log prefix to use for this handler
     */
    const LOG_PREFIX = null;

    //******************************************************************************
    //* Traits
    //******************************************************************************

    use EntityLookup, Lumberjack;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->initializeLumberjack($logger ?: \Log::getMonolog());
        static::LOG_PREFIX && $this->setLumberjackPrefix(static::LOG_PREFIX);
    }
}
