<?php namespace DreamFactory\Enterprise\Common\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

/**
 * A trait for working with Guzzle
 */
trait Guzzler
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use VerifiesSignatures;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type Client
     */
    protected $guzzleClient;
    /**
     * @type array The Guzzle configuration
     */
    protected $guzzleConfig;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Initialize and set up the guzzle client
     *
     * @param string $url         The url of the app server to use
     * @param array  $credentials Any credentials needed for the request [:client-id, :client-secret]
     * @param array  $config      Optional guzzle configuration options
     *
     * @return $this
     */
    public function createRequest($url, $credentials = [], $config = [])
    {
        $_endpoint = trim($url, '/ ') . '/';

        //  Check the endpoint...
        if (false === parse_url($_endpoint)) {
            throw new \InvalidArgumentException('The specified url "' . $url . '" is not valid.');
        }

        $this->guzzleConfig = array_merge($config, ['base_url' => $_endpoint]);
        $this->guzzleClient = new Client($this->guzzleConfig);

        //  Set credentials if provided
        if (isset($credentials, $credentials['client-id'], $credentials['client-secret'])) {
            $this->setSigningCredentials($credentials['client-id'], $credentials['client-secret']);
            unset($credentials['client-id'], $credentials['client-secret']);
        }

        return $this;
    }

    /**
     * Perform a GET
     *
     * @param string $uri
     * @param array  $payload
     * @param array  $options
     * @param bool   $object If true, the result is returned as an object instead of an array
     *
     * @return array|bool
     */
    public function guzzleGet($uri, $payload = [], $options = [], $object = true)
    {
        return $this->guzzleAny($uri, $payload, $options, Request::METHOD_GET, $object);
    }

    /**
     * Perform a POST
     *
     * @param string $uri
     * @param array  $payload
     * @param array  $options
     * @param bool   $object If true, the result is returned as an object instead of an array
     *
     * @return \stdClass|array|bool
     */
    public function guzzlePost($uri, $payload = [], $options = [], $object = true)
    {
        return $this->guzzleAny($uri, $payload, $options, Request::METHOD_POST, $object);
    }

    /**
     * Perform a DELETE
     *
     * @param string $uri
     * @param array  $payload
     * @param array  $options
     * @param bool   $object If true, the result is returned as an object instead of an array
     *
     * @return \stdClass|array|bool
     */
    public function guzzleDelete($uri, $payload = [], $options = [], $object = true)
    {
        return $this->guzzleAny($uri, $payload, $options, Request::METHOD_DELETE, $object);
    }

    /**
     * Perform a PUT
     *
     * @param string $uri
     * @param array  $payload
     * @param array  $options
     * @param bool   $object If true, the result is returned as an object instead of an array
     *
     * @return \stdClass|array|bool
     */
    public function guzzlePut($uri, $payload = [], $options = [], $object = true)
    {
        return $this->guzzleAny($uri, $payload, $options, Request::METHOD_PUT, $object);
    }

    /**
     * Performs a generic HTTP request
     *
     * @param string $url
     * @param array  $payload
     * @param array  $options
     * @param string $method
     * @param bool   $object If true, the result is returned as an object instead of an array
     *
     * @return array|bool|\stdClass
     */
    public function guzzleAny($url, $payload = [], $options = [], $method = Request::METHOD_POST, $object = true)
    {
        try {
            $_request = $this->getGuzzleClient()->createRequest($method,
                $url,
                array_merge($options, ['json' => $this->signRequest($payload)]));

            $_response = $this->guzzleClient->send($_request);

            return $_response->json(['object' => $object]);
        } catch (RequestException $_ex) {
            if ($_ex->hasResponse()) {
                $_response = $_ex->getResponse();
                if (false !== ($_data = json_decode($_response)) && JSON_ERROR_NONE == json_last_error()) {
                    return $_data;
                }
            }
        }

        return false;
    }

    /**
     * Returns the guzzle client
     *
     * @param array $config
     *
     * @return \GuzzleHttp\Client
     */
    public function getGuzzleClient($config = [])
    {
        if ($this->guzzleClient) {
            return $this->guzzleClient;
        }

        $this->guzzleConfig = $config;

        return $this->guzzleClient = new Client($this->guzzleConfig);
    }

    /**
     * @param Client $guzzleClient
     *
     * @return $this
     */
    public function setGuzzleClient($guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;

        return $this;
    }

    /**
     * @param array $guzzleConfig
     *
     * @return $this
     */
    public function setGuzzleConfig($guzzleConfig)
    {
        $this->guzzleConfig = $guzzleConfig;

        return $this;
    }

    /**
     * @return array
     */
    public function getGuzzleConfig()
    {
        return $this->guzzleConfig;
    }
}
