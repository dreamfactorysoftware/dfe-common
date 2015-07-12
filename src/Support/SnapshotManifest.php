<?php namespace DreamFactory\Enterprise\Common\Support;

use DreamFactory\Enterprise\Common\Enums\ManifestTypes;
use DreamFactory\Enterprise\Services\Console\Commands\Export;
use League\Flysystem\Filesystem;

class SnapshotManifest extends ExportManifest
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type array Basic metadata template
     */
    protected $allowedKeys = [
        'id',
        'type',
        'name',
        'instance-id',
        'cluster-id',
        'db-server-id',
        'app-server-id',
        'web-server-id',
        'owner-id',
        'owner-email-address',
        'owner-storage-key',
        'storage-key',
        'snapshot-prefix',
        'timestamp',
        'storage-export',
        'database-export',
        'hash',
        'link',
        self::CUSTODY_LOG_KEY,
    ];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function __construct($contents = [], $filename = null, Filesystem $filesystem = null, array $template = [])
    {
        parent::__construct(ManifestTypes::SNAPSHOT, $contents, $filename, $filesystem, $template);
    }
}