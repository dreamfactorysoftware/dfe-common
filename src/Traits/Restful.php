<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Library\Utility\Curl;
use DreamFactory\Library\Utility\Json;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;

/** Baseline REST interface */
trait Restful
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string The base uri to use
     */
    protected $baseUri;
    /**
     * @type array The request headers
     */
    protected $requestHeaders;

    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * @param string|null $uri
     * @param array       $payload
     * @param array       $options
     *
     * @return array|bool|\stdClass
     */
    public function get($uri = null, $payload = [], $options = [])
    {
        return $this->call($uri, $payload, $options, Request::METHOD_GET);
    }

    /**
     * @param string $uri
     * @param array  $payload
     * @param array  $options
     *
     * @return array|bool|\stdClass
     */
    public function post($uri, $payload = [], $options = [])
    {
        return $this->call($uri, $payload, $options, Request::METHOD_POST);
    }

    /**
     * @param string $uri
     * @param array  $payload
     * @param array  $options
     *
     * @return array|bool|\stdClass
     */
    public function put($uri, $payload = [], $options = [])
    {
        return $this->call($uri, $payload, $options, Request::METHOD_PUT);
    }

    /**
     * @param string $uri
     * @param array  $payload
     * @param array  $options
     *
     * @return array|bool|\stdClass
     */
    public function patch($uri, $payload = [], $options = [])
    {
        return $this->call($uri, $payload, $options, Request::METHOD_PATCH);
    }

    /**
     * @param string $uri
     * @param array  $payload
     * @param array  $options
     *
     * @return array|bool|\stdClass
     */
    public function delete($uri, $payload = [], $options = [])
    {
        return $this->call($uri, $payload, $options, Request::METHOD_DELETE);
    }

    /**
     * @param string $method The HTTP method to use
     * @param string $uri
     * @param array  $payload
     * @param array  $options
     *
     * @return array|bool|\stdClass
     */
    public function any($method, $uri, $payload = [], $options = [])
    {
        return $this->call($uri, $payload, $options, $method);
    }

    /**
     * Makes a shout out to an instance's private back-end. Should be called bootyCall()  ;)
     *
     * @param string $uri     The REST uri (i.e. "/[rest|api][/v[1|2]]/db", "/rest/system/users", etc.) to retrieve
     *                        from the instance
     * @param array  $payload Any payload to send with request
     * @param array  $options Any options to pass to transport layer
     * @param string $method  The HTTP method. Defaults to "POST"
     *
     * @return array|bool|\stdClass
     */
    public function call($uri, $payload = [], $options = [], $method = Request::METHOD_POST)
    {
        $options[CURLOPT_HTTPHEADER] = array_merge(data_get($options, CURLOPT_HTTPHEADER, []), $this->requestHeaders ?: []);

        if (!empty($payload) && !is_scalar($payload)) {
            $payload = Json::encode($payload);
            $options[CURLOPT_HTTPHEADER] = array_merge(data_get($options, CURLOPT_HTTPHEADER, []), ['Content-Type: application/json']);
        }

        try {
            $_response = Curl::request($method, $this->baseUri . ltrim($uri, ' /'), $payload, $options);

            $_info = Curl::getInfo();

            if (false === stripos($_info['content_type'], 'text/html') && Response::HTTP_OK != $_info['http_code']) {
                logger('[df.restful.' . $method . '] possible bad response: ' . print_r($_response, true));
            }
        } catch (Exception $_ex) {
            Log::error('[dfe.restful.' . $method . '] failure: ' . $_ex->getMessage());

            return false;
        }

        return $_response;
    }
}
