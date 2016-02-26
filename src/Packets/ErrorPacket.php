<?php namespace DreamFactory\Enterprise\Common\Packets;

use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ErrorPacket extends BasePacket
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public static function create($contents = null, $statusCode = Response::HTTP_NOT_FOUND, $errorMessage = null)
    {
        if ($contents instanceof \Exception) {
            $statusCode = $statusCode ?: (($contents instanceof HttpException) ? $contents->getStatusCode() : $contents->getCode());
            $errorMessage = $errorMessage ?: $contents->getMessage();
            $contents = null;
        }

        return parent::make(false, $contents, $statusCode, $errorMessage);
    }
}
