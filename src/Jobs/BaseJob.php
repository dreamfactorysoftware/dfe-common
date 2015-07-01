<?php namespace DreamFactory\Enterprise\Common\Jobs;

use DreamFactory\Enterprise\Common\Traits\HasResults;
use DreamFactory\Enterprise\Common\Traits\InteractsWithConsole;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Console\Input\InputAwareInterface;

/**
 * A base class for all "job" type commands (non-console)
 */
abstract class BaseJob implements ShouldQueue, InputAwareInterface
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

    use InteractsWithConsole,
        InteractsWithQueue,
        SerializesModels,
        HasResults;
}
