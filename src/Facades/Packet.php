<?php namespace DreamFactory\Enterprise\Common\Facades;

use DreamFactory\Enterprise\Common\Providers\PacketServiceProvider;
use Illuminate\Support\Facades\Facade;
use Symfony\Component\HttpFoundation\Response;

/**
 * Packet
 *
 * @method static array success($contents = null, $code = Response::HTTP_OK);
 * @method static array failure($contents = null, $code = Response::HTTP_OK, $message = null);
 */
class Packet extends Facade
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PacketServiceProvider::IOC_NAME;
    }

}
