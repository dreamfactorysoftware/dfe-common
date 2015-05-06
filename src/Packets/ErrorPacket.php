<?php
namespace DreamFactory\Enterprise\Common\Packets;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        $_ex = null;

        if ( $contents instanceof \Exception )
        {
            $_ex = $contents;
            $contents = null;
            $statusCode = $_ex->getCode();
            $message = $message ?: $_ex->getMessage();
        }
        elseif ( $message instanceof \Exception )
        {
            $_ex = $message;
            $statusCode = $_ex->getCode();
            $message = $_ex->getMessage();
        }

        $_packet = static::_create( false, $contents, $statusCode );

        if ( $_ex )
        {
            $_packet['error'] = array(
                'message' => $message,
                'code'    => $statusCode,
            );

            if ( in_array( $statusCode, range( Response::HTTP_MULTIPLE_CHOICES, Response::HTTP_PERMANENTLY_REDIRECT ) ) )
            {
                if ( method_exists( $_ex, 'getRedirectUri' ) )
                {
                    $_packet['location'] = $_ex->getRedirectUri();
                }
            }
        }

        return $_packet;
    }

    /**
     * --1Same as make but different arg order
     *
     * @param int               $statusCode
     * @param string|\Exception $message
     * @param mixed             $contents
     *
     * @return array The packetized contents
     */
    public static function create( $statusCode = Response::HTTP_NOT_FOUND, $message = null, $contents = null )
    {
        if ( $statusCode instanceof HttpException )
        {
            $_ex = $statusCode;
            $statusCode = $_ex->getStatusCode();
            $message = $_ex->getMessage();
        }

        return static::make( $contents, $statusCode, $message );
    }
}