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
        $_ex = null;
        $_packet = static::_create( false, $contents, $statusCode );

        if ( $contents instanceof \Exception )
        {
            $_ex = $contents;
        }
        elseif ( $message instanceof \Exception )
        {
            $_ex = $message;
        }

        if ( $_ex )
        {
            $_packet['error'] = array(
                'message' => $_ex->getMessage(),
                'code'    => $_code = $_ex->getCode(),
            );

            if ( in_array( $_code, range( Response::HTTP_MULTIPLE_CHOICES, Response::HTTP_PERMANENTLY_REDIRECT ) ) )
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
        return static::make( $contents, $statusCode, $message );
    }
}