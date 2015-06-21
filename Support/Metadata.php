<?php namespace DreamFactory\Enterprise\Common\Support;

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
        'db'    => [],
        'paths' => [
            'storage-path'       => null,
            'private-path'       => null,
            'owner-private-path' => null,
            'snapshot-path'      => null,
        ],
        'env'   => [
            'private-path-name'  => '.private',
            'snapshot-path-name' => 'snapshots',
            'cluster-id'         => null,
            'default-domain'     => null,
            'signature-method'   => null,
            'storage-root'       => null,
            'console-api-url'    => null,
            'console-api-key'    => null,
            'client-id'          => null,
            'client-secret'      => null,
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