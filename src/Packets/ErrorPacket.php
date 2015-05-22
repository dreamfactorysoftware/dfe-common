<?php
namespace DreamFactory\Enterprise\Common\Packets;

use Symfony\Component\HttpFoundation\Response;

class ErrorPacket extends BasePacket
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param mixed             $contents
     * @param int               $statusCode
     * @param string|\Exception $message
     *
     * @return array The packetized contents
     */
    public static function make( $contents = null, $statusCode = Response::HTTP_NOT_FOUND, $message = null )
    {
        if ( $contents instanceof \Exception )
        {
            return static::makeFromException( $contents );
        }

        $_packet = static::_create( false, $contents, $statusCode );
        $_packet['error']['message'] = $message;

        return $_packet;
    }

    /**
     * @param \Exception $exception
     * @param mixed      $contents
     *
     * @return array
     */
    public static function makeFromException( \Exception $exception, $contents = null )
    {
        $_packet = static::make( $contents, $exception->getCode(), $exception->getMessage() );

        if ( in_array( $exception->getCode(), range( Response::HTTP_MULTIPLE_CHOICES, Response::HTTP_PERMANENTLY_REDIRECT ) ) )
        {
            if ( method_exists( $exception, 'getRedirectUri' ) )
            {
                $_packet['location'] = $exception->getRedirectUri();
            }
        }

        return $_packet;
    }

    /**
     * Same as make but different arg order
     *
     * @param int               $statusCode
     * @param string|\Exception $message
     * @param mixed             $contents
     *
     * @return array The packetized contents
     */
    public static function create( $statusCode = Response::HTTP_NOT_FOUND, $message = null, $contents = null )
    {
        if ( $statusCode instanceof \Exception )
        {
            return static::makeFromException( $statusCode, $contents );
        }

        return static::make( $contents, $statusCode, $message );
    }
}