<?php
namespace DreamFactory\Enterprise\Common\Commands;

use DreamFactory\Enterprise\Common\Exceptions\NotImplementedException;
use DreamFactory\Enterprise\Common\Traits\HasResults;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * A base class for all "job" type commands (non-console)
 */
abstract class JobCommand implements ShouldBeQueued
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string|bool The queue upon which to push myself. Set to false to not use queuing
     */
    const JOB_QUEUE = false;

    //******************************************************************************
    //* Traits
    //******************************************************************************

    use InteractsWithQueue, SerializesModels, HasResults;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return string The handler class for this job if different from "[class-name]Handler"
     * @throws NotImplementedException
     */
    public function getHandler()
    {
        throw new NotImplementedException( 'No handler defined for this job type.' );
    }

}
