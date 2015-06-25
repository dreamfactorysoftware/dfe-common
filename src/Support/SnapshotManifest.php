<?php namespace DreamFactory\Enterprise\Common\Support;

use DreamFactory\Enterprise\Common\Enums\ManifestTypes;
use League\Flysystem\Filesystem;

class SnapshotManifest extends Manifest
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type array Basic metadata template
     */
    protected $template = [
        'id'              => null,
        'type'            => null,
        'snapshot-prefix' => null,
        'timestamp'       => null,
        'database-export' => null,
        'storage-export'  => null,
        'hash'            => null,
        'link'            => null,

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