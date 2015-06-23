<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Common\Enums\EnterpriseDefaults;
use DreamFactory\Enterprise\Database\Models\AppKey;

/**
 * A trait that adds signature verification functionality
 *
 * Be sure to call setSigningCredentials() before trying to verify
 */
trait VerifiesSignatures
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string
     */
    protected $_vsSignature = null;
    /**
     * @type string
     */
    protected $_vsClientId = null;
    /**
     * @type string
     */
    protected $_vsClientSecret = null;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Validates a client key pair and generates a signature for verification.
     *
     * @param string $clientId
     * @param string $clientSecret
     *
     * @return $this
     */
    protected function setSigningCredentials($clientId, $clientSecret)
    {
        $_key = AppKey::byClientId($clientId)->first();

        if (empty($_key) || $clientSecret != $_key->client_secret) {
            throw new \InvalidArgumentException('Invalid credentials.');
        }

        //  Looks good
        $this->_vsClientId = $_key->client_id;
        $this->_vsClientSecret = $_key->client_secret;
        $this->_vsSignature = $this->_generateSignature();

        return $this;
    }

    /**
     * @param string $token        The client-provided "access token"
     * @param string $clientId     The client-provided "client-id"
     * @param string $clientSecret The actual client-secret associated with client-provided "client-id"
     *
     * @return bool
     */
    protected function _verifySignature($token, $clientId, $clientSecret)
    {
        return $token === $this->_vsSignature;
    }

    /**
     * @return string
     */
    private function _generateSignature()
    {
        return hash_hmac(
            config('dfe.signature-method', EnterpriseDefaults::DEFAULT_SIGNATURE_METHOD),
            $this->_vsClientId,
            $this->_vsClientSecret
        );
    }

    /**
     * @param array $payload
     *
     * @return array
     */
    protected function _signRequest(array $payload)
    {
        return array_merge(
            [
                'client-id'    => $this->_vsClientId,
                'access-token' => $this->_vsSignature,
            ],
            $payload ?: []
        );
    }
}
