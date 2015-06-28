<?php namespace DreamFactory\Enterprise\Common\Listeners;

use DreamFactory\Enterprise\Common\Traits\EntityLookup;
use DreamFactory\Enterprise\Common\Traits\Lumberjack;

/**
 * A base class for listeners. Includes entity lookup and logging
 */
class BaseListener
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use EntityLookup, Lumberjack;
}
