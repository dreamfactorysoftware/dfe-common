<?php namespace DreamFactory\Enterprise\Common\Provisioners;

use DreamFactory\Enterprise\Common\Traits\HasResults;
use Illuminate\Http\Response;

class BaseResponse extends Response
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use HasResults;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type mixed|null The output, if any, of the provisioning request
     */
    protected $output;
    /**
     * @type BaseRequest The original request
     */
    protected $request;
    /**
     * @type bool Self-describing
     */
    protected $success = false;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Success response factory
     *
     * @param BaseRequest $request  The request triggering this response
     * @param mixed|null  $result   Result of the operation
     * @param mixed|null  $output   Any output from the operation
     * @param int|null    $httpCode The code to return
     * @param array       $headers  Any headers to include with the response
     *
     * @return static
     */
    public static function makeSuccess($request, $result = null, $output = null, $httpCode = null, $headers = [])
    {
        $_response = new static($request, $httpCode ?: Response::HTTP_OK, $headers);
        /** @noinspection PhpUndefinedMethodInspection */
        $_response
            ->setResult($result)
            ->setSuccess(true)
            ->setOutput($output)
            ->setRequest($request);
    }

    /**
     * Failure response factory
     *
     * @param BaseRequest $request  The request triggering this response
     * @param mixed|null  $result   Result of the operation
     * @param mixed|null  $output   Any output from the operation
     * @param int|null    $httpCode The code to return
     * @param array       $headers  Any headers to include with the response
     *
     * @return static
     */
    public static function makeFailure($request, $result = null, $output = null, $httpCode = null, $headers = [])
    {
        $_response = new static($request, $httpCode ?: Response::HTTP_OK, $headers);
        /** @noinspection PhpUndefinedMethodInspection */
        $_response
            ->setResult($result)
            ->setSuccess(false)
            ->setOutput($output)
            ->setRequest($request);
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @return mixed|null
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return BaseRequest|ProvisionServiceRequest|PortableServiceRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \DreamFactory\Enterprise\Database\Models\Instance
     */
    public function getInstance()
    {
        return $this->getRequest()->getInstance();
    }

    /**
     * @param boolean $success
     *
     * @return $this
     */
    public function setSuccess($success)
    {
        $this->success = !!$success;

        return $this;
    }

    /**
     * @param mixed|null $output
     *
     * @return $this
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @param BaseRequest $request
     *
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

}
