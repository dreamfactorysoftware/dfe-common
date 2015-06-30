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
    }
}
