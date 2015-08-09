<?php namespace DreamFactory\Enterprise\Common\Facades;

use DreamFactory\Enterprise\Common\Providers\InstanceStorageServiceProvider;
use DreamFactory\Enterprise\Database\Models\Instance;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string getStoragePath(Instance $instance)
 * @method static string getTrashPath(Instance $instance, string $append = null, bool $create = true)
 * @method static string getSnapshotPath(Instance $instance)
 * @method static string getPrivatePathName()
 * @method static string getPrivatePath(Instance $instance)
 * @method static string getOwnerPrivatePath(Instance $instance)
 * @method static string getWorkPath(Instance $instance, string $append = null)
 * @method static string deleteWorkPath(string $workPath)
 * @method static FilesystemAdapter getRootStorageMount(Instance $instance, string $path = null, string $tag = 'root-storage-mount')
 * @method static FilesystemAdapter getTrashMount(Instance $instance, string $append = null, bool $create = true)
 * @method static FilesystemAdapter getStorageMount(Instance $instance, string $tag = 'root-storage-mount')
 * @method static FilesystemAdapter getSnapshotMount(Instance $instance, string $tag = 'snapshot-mount')
 * @method static FilesystemAdapter getPrivateStorageMount(Instance $instance, string $tag = 'private-storage')
 * @method static FilesystemAdapter getOwnerPrivateStorageMount(Instance $instance, string $tag = 'owner-private-storage')
 */
class InstanceStorage extends Facade
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return string
     */
    /** @noinspection PhpMissingParentCallCommonInspection */
    protected static function getFacadeAccessor()
    {
        return InstanceStorageServiceProvider::IOC_NAME;
    }
}