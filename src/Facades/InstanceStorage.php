<?php namespace DreamFactory\Enterprise\Common\Facades;

use DreamFactory\Enterprise\Common\Providers\InstanceStorageServiceProvider;
use DreamFactory\Enterprise\Common\Services\InstanceStorageService;
use DreamFactory\Enterprise\Database\Models\Instance;
use Illuminate\Support\Facades\Facade;
use League\Flysystem\Filesystem;

/**
 * @method static string|bool buildStorageMap(string $hashBase = null)
 * @method static InstanceStorageService setInstance(Instance $instance)
 * @method static string getPrivatePathName()
 * @method static string getStorageRootPath(string $append = null)
 * @method static string getTrashPath(string $append = null, bool $create = true)
 * @method static string getStoragePath(Instance $instance, string $append = null, bool $create = false)
 * @method static string getPrivatePath(Instance $instance, string $append = null, bool $create = false)
 * @method static string getOwnerPrivatePath(Instance $instance, string $append = null, boolean $create = false)
 * @method static string getSnapshotPath(Instance $instance, string $append = null, bool $create = false)
 * @method static string getWorkPath(Instance $instance, string $append = null)
 * @method static string deleteWorkPath(string $workPath)
 * @method static Filesystem getTrashMount(string $append = null, bool $create = true)
 * @method static Filesystem getStorageRootMount(Instance $instance, $tag = 'storage-root:instance-id')
 * @method static Filesystem getStorageMount(Instance $instance, string $tag = 'storage:instance-id')
 * @method static Filesystem getPrivateStorageMount(Instance $instance, string $tag = 'private:instance-id')
 * @method static Filesystem getOwnerPrivateStorageMount(Instance $instance, string $tag = 'owner-private:instance-id')
 * @method static Filesystem getSnapshotMount(Instance $instance, string $tag = 'snapshot:instance-id')
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