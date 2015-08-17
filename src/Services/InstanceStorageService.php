<?php namespace DreamFactory\Enterprise\Common\Services;

use DreamFactory\Enterprise\Common\Enums\EnterpriseDefaults;
use DreamFactory\Enterprise\Database\Enums\GuestLocations;
use DreamFactory\Enterprise\Database\Models\Instance;
use DreamFactory\Library\Utility\Disk;
use DreamFactory\Library\Utility\Exceptions\DiskException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

/**
 * Core instance storage services
 */
class InstanceStorageService extends BaseService
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string
     */
    protected $privatePathName = EnterpriseDefaults::PRIVATE_PATH_NAME;
    /**
     * @type string The current storage root
     */
    protected $storageRoot = EnterpriseDefaults::STORAGE_ROOT;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Init
     */
    public function boot()
    {
        //  Set our master stuff
        $this->privatePathName =
            trim(config('provisioning.private-path-name', EnterpriseDefaults::PRIVATE_PATH_NAME),
                DIRECTORY_SEPARATOR . ' ');

        $this->storageRoot =
            trim(config('provisioning.storage-root', EnterpriseDefaults::STORAGE_ROOT), DIRECTORY_SEPARATOR . ' ');
    }

    /**
     * Returns the "storage-root" as defined in config/provisioning.php. No existence checks are performed
     *
     * @return string
     */
    public function getStorageRoot()
    {
        return $this->storageRoot;
    }

    /**
     * Returns the ROOT storage path for a user. Under which is all instances and private areas
     *
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string                                            $append
     * @param bool                                              $create
     *
     * @return mixed|string
     */
    public function getUserStoragePath(Instance $instance, $append = null, $create = false)
    {
        static $_cache = [];

        $_ck =
            hash(EnterpriseDefaults::DEFAULT_SIGNATURE_METHOD,
                'rsp.' . $instance->instance_id_text . Disk::segment($append, true));

        if (!is_numeric($instance->guest_location_nbr)) {
            $instance->guest_location_nbr = GuestLocations::resolve($instance->guest_location_nbr, true);
        }

        if (null === ($_path = array_get($_cache, $_ck))) {
            switch ($instance->guest_location_nbr) {
                case GuestLocations::DFE_CLUSTER:
                    $_path = Disk::path([$this->getStorageRoot(), $instance->getSubRootHash(), $append], $create);
                    break;

                default:
                    $_path = storage_path($append);
                    break;
            }

            $_cache[$_ck] = $_path;
        }

        \Log::debug('storage path: ' . $_path);

        return $_path;
    }

    /**
     * Returns the absolute path to an instance's /storage root
     *
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string|null                                       $append Optional path to append
     * @param bool                                              $create
     *
     * @return string
     */
    public function getStoragePath(Instance $instance, $append = null, $create = false)
    {
        static $_path;

        return $_path
            ?:
            $_path = Disk::path([$this->getUserStoragePath($instance), $instance->instance_id_text, $append], $create);
    }

    /**
     * @param string|null $append
     * @param bool        $create If true, create when non-existent
     *
     * @return string
     */
    public function getTrashPath($append = null, $create = true)
    {
        static $_path;

        return $_path ?: $_path = Disk::path([config('snapshot.trash-path'), $append], $create);
    }

    /**
     * @param string|null $append
     * @param bool        $create If true, create when non-existent
     *
     * @return string
     */
    public function getTrashMount($append = null, $create = true)
    {
        return new Filesystem(new Local($this->getTrashPath($append, $create)));
    }

    /**
     * @return string
     */
    public function getPrivatePathName()
    {
        return $this->privatePathName;
    }

    /**
     * We want the private path of the instance to point to the user's area. Instances have no "private path" per se.
     *
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string|null                                       $append Optional path to append
     * @param bool                                              $create
     *
     * @return mixed
     */
    public function getPrivatePath(Instance $instance, $append = null, $create = false)
    {
        static $_path;

        return $_path
            ?: $_path = Disk::path([$this->getStoragePath($instance), $this->getPrivatePathName(), $append], $create);
    }

    /**
     * We want the private path of the instance to point to the user's area. Instances have no "private path" per se.
     *
     * @param string|null $append Optional path to append
     * @param bool        $create
     *
     * @return mixed
     */
    public function getOwnerPrivatePath($append = null, $create = false)
    {
        static $_path;

        return $_path ?: $_path = Disk::path([$this->getStorageRoot(), $this->getPrivatePathName(), $append], $create);
    }

    /**
     * @param string|null $append Optional path to append
     * @param bool        $create
     *
     * @return string
     */
    public function getSnapshotPath($append = null, $create = false)
    {
        return Disk::path([
            $this->getOwnerPrivatePath(),
            config('provisioning.snapshot-path-name', EnterpriseDefaults::SNAPSHOT_PATH_NAME),
            $append,
        ], $create);
    }

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string|null                                       $append Optional appendage to path
     *
     * @return string
     * @throws DiskException
     */
    public function getWorkPath(Instance $instance, $append = null)
    {
        //  Try private temp path or default to system temp
        if (false === ($_workPath = Disk::path([$instance->getPrivatePath(), 'tmp', $append], true))) {
            $_workPath = Disk::path([sys_get_temp_dir(), 'dfe', $instance->instance_id_text, $append], true);

            if (!$_workPath) {
                throw new DiskException('Unable to locate a suitable temporary directory.');
            }
        }

        return $_workPath;
    }

    /**
     * @param string $workPath
     *
     * @return bool
     */
    public function deleteWorkPath($workPath)
    {
        return is_dir($workPath) && Disk::rmdir($workPath, true);
    }

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string                                            $tag
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function getStorageMount(Instance $instance, $tag = null)
    {
        return $this->mount($instance, $this->getStoragePath($instance),
            $tag ?: 'storage:' . $instance->instance_id_text);
    }

    /**
     * Returns the relative root directory of this instance's storage
     *
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string                                            $tag
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function getSnapshotMount(Instance $instance, $tag = null)
    {
        return $this->mount($instance, $this->getSnapshotPath($instance),
            $tag ?: 'snapshots:' . $instance->instance_id_text);
    }

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string                                            $tag
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function getPrivateStorageMount(Instance $instance, $tag = null)
    {
        return $this->mount($instance, $this->getPrivatePath($instance),
            $tag ?: 'private-storage:' . $instance->instance_id_text);
    }

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string                                            $tag
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function getOwnerPrivateStorageMount(Instance $instance, $tag = null)
    {
        return $this->mount($instance, $this->getOwnerPrivatePath(),
            $tag ?: 'owner-private-storage:' . $instance->instance_id_text);
    }

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string                                            $path
     * @param string                                            $tag
     * @param array                                             $options
     *
     * @return Filesystem
     */
    protected function mount($instance, $path, $tag = null, $options = [])
    {
        if (!$instance->webServer) {
            throw new \InvalidArgumentException('No configured web server for instance.');
        }

        $_mount = $instance->webServer->mount;

        if (!$_mount) {
            throw new \RuntimeException('The web server "' .
                $instance->webServer->server_id_text .
                '" does not have a mount defined.');
        }

        return $_mount->getFilesystem($path, $tag, $options);
    }
}