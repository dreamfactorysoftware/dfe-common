<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Common\Enums\EnterpriseDefaults;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A trait that adds signature verification functionality
 */
trait VerifiesSignatures
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type OutputInterface
     */
    protected $_signatureMethod;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string $token        The client-provided "access token"
     * @param string $clientId     The client-provided "client-id"
     * @param string $clientSecret The actual client-secret associated with client-provided "client-id"
     *
     * @return bool
     */
    protected function _verifySignature( $token, $clientId, $clientSecret )
    {
        return
            $token === $this->_generateSignature( $clientId, $clientSecret );
    }

    /**
     * @param string $clientId
     * @param string $clientSecret
     *
     * @return string
     */
    protected function _generateSignature( $clientId, $clientSecret )
    {
        !$this->_signatureMethod && ( $this->_signatureMethod = config( 'dfe.signature-method', EnterpriseDefaults::DEFAULT_SIGNATURE_METHOD ) );

        return hash_hmac( $this->_signatureMethod, $clientId, $clientSecret );
    }
}
