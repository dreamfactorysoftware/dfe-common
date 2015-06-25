<?php namespace DreamFactory\Enterprise\Common\Support;

use DreamFactory\Enterprise\Common\Enums\EnterpriseDefaults;
use DreamFactory\Enterprise\Common\Enums\ManifestTypes;
use League\Flysystem\Filesystem;

class Metadata extends Manifest
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type array Basic metadata template
     */
    protected $template = [
        'db'          => [],
        'env'         => [
            'cluster-id'       => null,
            'default-domain'   => null,
            'signature-method' => EnterpriseDefaults::SIGNATURE_METHOD,
            'storage-root'     => null,
            'console-api-url'  => null,
            'console-api-key'  => null,
            'client-id'        => null,
            'client-secret'    => null,
            'partner-id'       => null,
        ],
        'paths'       => [
            'private-path'       => null,
            'owner-private-path' => null,
            'snapshot-path'      => null,
        ],
        'storage-map' => [
            'zone'      => null,
            'partition' => null,
            'root-hash' => null,
        ],
    ];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function __construct(Filesystem $filesystem, $contents = [], $filename = null)
    {
        parent::__construct($filesystem,
            ManifestTypes::METADATA,
            $contents,
            $filename);
    }

}