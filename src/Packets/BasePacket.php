<?php namespace DreamFactory\Enterprise\Common\Packets;

use DreamFactory\Library\Utility\Json;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BasePacket
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string The version of this packet
     */
    const PACKET_VERSION = '2.0';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Builds a v2 response container
     *
     * @param bool              $success  The success indicator
     * @param mixed|null        $contents The contents
     * @param int               $httpCode
     *
     * @param string|\Exception $errorMessage
     *
     * @return array
     */
    public static function make($success = true, $contents = null, $httpCode = Response::HTTP_OK, $errorMessage = null)
    {
        //  All packets have this
        return static::makePacket($success, $contents, $httpCode, $errorMessage);
    }

    /**
     * @param bool                   $success  True if success
     * @param int|null               $httpCode The HTTP status to return
     * @param mixed|array|null       $contents The payload to return
     * @param string|\Exception|null $errorMessage
     *
     * @return array
     */
    protected static function makePacket($success, $contents = null, $httpCode = Response::HTTP_OK, $errorMessage = null)
    {
        return static::signPacket([
            'success'     => $success,
            'status_code' => $httpCode,
            'response'    => $contents,
        ],
            $httpCode,
            $errorMessage);
    }

    /**
     * Generates a signature for a packet request response
     *
     * @param array                  $packet
     * @param int|\Exception|null    $code
     * @param string|\Exception|null $message
     *
     * @return array
     *
     */
    protected static function signPacket(array $packet, $code = null, $message = null)
    {
        $_ex = false;

        if ($code instanceof \Exception) {
            $_ex = $code;
            $code = null;
        } elseif ($message instanceof \Exception) {
            $_ex = $message;
            $message = null;
        }

        if (false !== $_ex) {
            (null === $code) && $code = $_ex->getCode();
            (null === $message) && $message = $_ex->getMessage();
        }

        $_startTime = \Request::server('REQUEST_TIME_FLOAT', \Request::server('REQUEST_TIME', $_timestamp = microtime(true)));

        $_elapsed = $_timestamp - $_startTime;
        $_id = sha1($_startTime . \Request::server('HTTP_HOST') . \Request::server('REMOTE_ADDR'));

        //  All packets have this
        $_packet = array_merge($packet,
            [
                'error'       => false,
                'status_code' => $code,
                'request'     => [
                    'id'          => $_id,
                    'version'     => static::PACKET_VERSION,
                    'signature'   => base64_encode(hash_hmac(config('dfe.signature-method'), $_id, $_id, true)),
                    'verb'        => \Request::method(),
                    'request-uri' => \Request::getRequestUri(),
                    'start'       => date('c', $_startTime),
                    'elapsed'     => (float)number_format($_elapsed, 4),
                ],
            ]);

        //  Update the error entry if there was an error
        if (!array_get($packet, 'success', false) && !array_get($packet, 'error', false)) {
            $_packet['error'] = [
                'code'    => $code,
                'message' => $message,
            ];

            $_ex && $_packet['error']['exception'] = Json::encode($_ex);
        } else {
            array_forget($_packet, 'error');
        }

        return $_packet;
    }
}
