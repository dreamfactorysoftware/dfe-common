<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Library\Utility\Curl;
use DreamFactory\Library\Utility\Json;
use DreamFactory\Library\Utility\Uri;
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
     * @type string The base resource uri to use
     */
    protected $resourceUri;
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
     * @param bool        $excludeResource If true, the resourceUri will not be prepended to $uri
     *
     * @return array|bool|\stdClass
     */
    public function get($uri = null, $payload = [], $options = [], $excludeResource = false)
    {
        return $this->call($uri, $payload, $options, Request::METHOD_GET, $excludeResource);
    }

    /**
     * @param string $uri
     * @param array  $payload
     * @param array  $options
     * @param bool   $excludeResource If true, the resourceUri will not be prepended to $uri
     *
     * @return array|bool|\stdClass
     */
    public function post($uri, $payload = [], $options = [], $excludeResource = false)
    {
        return $this->call($uri, $payload, $options, Request::METHOD_POST, $excludeResource);
    }

    /**
     * @param string $uri
     * @param array  $payload
     * @param array  $options
     * @param bool   $excludeResource If true, the resourceUri will not be prepended to $uri
     *
     * @return array|bool|\stdClass
     */
    public function put($uri, $payload = [], $options = [], $excludeResource = false)
    {
        return $this->call($uri, $payload, $options, Request::METHOD_PUT, $excludeResource);
    }

    /**
     * @param string $uri
     * @param array  $payload
     * @param array  $options
     * @param bool   $excludeResource If true, the resourceUri will not be prepended to $uri
     *
     * @return array|bool|\stdClass
     */
    public function patch($uri, $payload = [], $options = [], $excludeResource = false)
    {
        return $this->call($uri, $payload, $options, Request::METHOD_PATCH, $excludeResource, $excludeResource);
    }

    /**
     * @param string $uri
     * @param array  $payload
     * @param array  $options
     * @param bool   $excludeResource If true, the resourceUri will not be prepended to $uri
     *
     * @return array|bool|\stdClass
     */
    public function delete($uri, $payload = [], $options = [], $excludeResource = false)
    {
        return $this->call($uri, $payload, $options, Request::METHOD_DELETE, $excludeResource);
    }

    /**
     * @param string $method          The HTTP method to use
     * @param string $uri
     * @param array  $payload
     * @param array  $options
     * @param bool   $excludeResource If true, the resourceUri will not be prepended to $uri
     *
     * @return array|bool|\stdClass
     */
    public function any($method, $uri, $payload = [], $options = [], $excludeResource = false)
    {
        return $this->call($uri, $payload, $options, $method, $excludeResource);
    }

    /**
     * Makes a shout out to an instance's private back-end. Should be called bootyCall()  ;)
     *
     * @param string $uri             The REST uri (i.e. "/[rest|api][/v[1|2]]/db", "/rest/system/users", etc.) to retrieve
     *                                from the instance
     * @param array  $payload         Any payload to send with request
     * @param array  $options         Any options to pass to transport layer
     * @param string $method          The HTTP method. Defaults to "POST"
     * @param bool   $excludeResource If true, the resourceUri will not be prepended to $uri
     *
     * @return array|bool|\stdClass
     */
    public function call($uri, $payload = [], $options = [], $method = Request::METHOD_POST, $excludeResource = false)
    {
        $options[CURLOPT_HTTPHEADER] = array_merge(data_get($options, CURLOPT_HTTPHEADER, []), $this->requestHeaders ?: []);

        if (!empty($payload) && !is_scalar($payload)) {
            $payload = Json::encode($payload);
            $options[CURLOPT_HTTPHEADER] = array_merge(data_get($options, CURLOPT_HTTPHEADER, []), ['Content-Type: application/json']);
        }

        try {
            $uri = $excludeResource ? Uri::segment([$this->baseUri, $uri], false) : Uri::segment([$this->baseUri, $this->resourceUri, $uri], false);
            $_response = Curl::request($method, $uri, $payload, $options);

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
