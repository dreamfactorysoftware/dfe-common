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
     * @param string $baseUri The optional base url to use
     * @param array  $config  Optional guzzle configuration options
     *
     * @return $this
     */
    public function createClient($baseUri = null, $config = [])
    {
        //  Check the endpoint...
        if ($baseUri && false === parse_url($baseUri)) {
            throw new \InvalidArgumentException('The specified url "' . $baseUri . '" is not valid.');
        }

        $_options = ['debug' => env('APP_DEBUG', false)];
        $baseUri && $_options['base_uri'] = $baseUri;

        $this->guzzleConfig = array_merge($config, $_options);
        $this->guzzleClient = new Client($this->guzzleConfig);

        return $this;
    }

    /**
     * Initialize and set up the guzzle client with signing credentials
     *
     * @param string $url         The url of the app server to use
     * @param array  $credentials Any credentials needed for the request [:client-id, :client-secret]
     * @param array  $config      Optional guzzle configuration options
     *
     * @return $this
     */
    public function createRequest($url, $credentials = [], $config = [])
    {
        $this->createClient($url, $config);

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
            if (!empty($payload) && !is_scalar($payload)) {
                array_merge($options, ['json' => json_encode($payload)]);
            }

            $_response = call_user_func_array([$this->getGuzzleClient(), $method], [$url, $options,]);

            return $_response->json(['object' => $object]);
        } catch (RequestException $_ex) {
            $_response = $_ex->hasResponse() ? $_ex->getResponse() : null;

            $_body = trim((string)$_response->getBody());

            return $_body ?: $_response;
        }
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
     * @return array
     */
    public function getGuzzleConfig()
    {
        return $this->guzzleConfig;
    }
}
