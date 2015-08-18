<?php namespace DreamFactory\Enterprise\Common\Facades;

use DreamFactory\Enterprise\Common\Providers\InstanceStorageServiceProvider;
use DreamFactory\Enterprise\Database\Models\Instance;
use Illuminate\Support\Facades\Facade;
use League\Flysystem\Filesystem;

/**
 * @method static string getStorageRoot()
 * @method static string getTrashPath(string $append = null, bool $create = true)
 * @method static string getSnapshotPath(string $append = null, bool $create = false)
 * @method static string getUserStoragePath(Instance $instance, string $append = null)
 * @method static string getStoragePath(Instance $instance, string $append = null)
 * @method static string getPrivatePathName()
 * @method static string getPrivatePath(Instance $instance)
 * @method static string getOwnerPrivatePath(string $append = null, boolean $create = false)
 * @method static string getWorkPath(Instance $instance, string $append = null)
 * @method static string deleteWorkPath(string $workPath)
 * @method static Filesystem getStorageRootMount()
 * @method static Filesystem getTrashMount(Instance $instance, string $append = null, bool $create = true)
 * @method static Filesystem getUserStorageMount(Instance $instance, string $append = null)
 * @method static Filesystem getStorageMount(Instance $instance, string $tag = 'root-storage-mount')
 * @method static Filesystem getSnapshotMount(Instance $instance, string $tag = 'snapshot-mount')
 * @method static Filesystem getPrivateStorageMount(Instance $instance, string $tag = 'private-storage')
 * @method static Filesystem getOwnerPrivateStorageMount(Instance $instance, string $tag = 'owner-private-storage')
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