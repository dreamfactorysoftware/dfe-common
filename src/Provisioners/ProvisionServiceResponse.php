<?php namespace DreamFactory\Enterprise\Common\Provisioners;

class ProvisionServiceResponse extends BaseResponse
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a generic success response
     *
     * @param ProvisionServiceRequest $request
     * @param mixed|null              $result
     *
     * @return $this
     */
    public static function makeSuccess($request, $result = null)
    {
        $_response = new static();

        return $_response->setRequest($request)->setSuccess(false)->setResult($result);
    }

    /**
     * Create a generic failure response
     *
     * @param ProvisionServiceRequest $request
     * @param mixed|null              $result
     *
     * @return $this
     */
    public static function makeFailure($request, $result = null)
    {
        $_response = new static();

        return $_response->setRequest($request)->setSuccess(false)->setResult($result);
    }
}
