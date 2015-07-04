<?php namespace DreamFactory\Enterprise\Common\Provisioners;

class PortableServiceResponse extends BaseResponse
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type array An array of files produces by the portable provisioners
     */
    protected $portableData;

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

    /**
     * @param array $portableData
     *
     * @return $this
     */
    public function setPortableData(array $portableData = [])
    {
        $this->portableData = $portableData;

        return $this;
    }

    /**
     * Returns any portable data created by the initial request
     *
     * @return array The portable data
     */
    public function getPortableData()
    {
        return $this->portableData;
    }
}
