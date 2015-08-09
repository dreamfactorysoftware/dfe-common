<?php namespace DreamFactory\Enterprise\Common\Services;

use DreamFactory\Enterprise\Common\Enums\EnterpriseDefaults;
use DreamFactory\Enterprise\Common\Exceptions\DiskException;
use DreamFactory\Enterprise\Common\Utility\Disk;
use DreamFactory\Enterprise\Database\Models\Instance;
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

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Init
     */
    public function boot()
    {
        $this->privatePathName =
            $this->cleanPath(config('provisioning.private-path-name', EnterpriseDefaults::PRIVATE_PATH_NAME),
                false,
                true);
    }

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string                                            $append
     *
     * @return string
     */
    public function getRootStoragePath(Instance $instance, $append = null)
    {
        return $instance->getRootStoragePath($append);
    }

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string|null                                       $append Optional path to append
     *
     * @return string
     */
    public function getStoragePath(Instance $instance, $append = null)
    {
        return $this->getRootStoragePath($instance, $instance->instance_id_text) . Disk::segment($append);
    }

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string|null                                       $append
     * @param bool                                              $create If true, create when non-existant
     *
     * @return string
     */
    public function getTrashPath(Instance $instance, $append = null, $create = true)
    {
        return $instance->getTrashPath($append, $create);
    }

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string|null                                       $append
     * @param bool                                              $create If true, create when non-existant
     *
     * @return string
     */
    public function getTrashMount(Instance $instance, $append = null, $create = true)
    {
        return new Filesystem(new Local($this->getTrashPath($instance, $append, $create)));
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
     *
     * @return mixed
     */
    public function getPrivatePath(Instance $instance, $append = null)
    {
        return $this->getStoragePath($instance) . DIRECTORY_SEPARATOR . $this->getPrivatePathName() . Disk::segment($append);
    }

    /**
     * We want the private path of the instance to point to the user's area. Instances have no "private path" per se.
     *
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string|null                                       $append Optional path to append
     *
     * @return mixed
     */
    public function getOwnerPrivatePath(Instance $instance, $append = null)
    {
        return $this->getRootStoragePath($instance, $this->getPrivatePathName()) . Disk::segment($append);
    }

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string|null                                       $append Optional path to append
     *
     * @return string
     */
    public function getSnapshotPath(Instance $instance, $append = null)
    {
        return $this->getOwnerPrivatePath($instance) . Disk::segment([
            config('provisioning.snapshot-path-name', EnterpriseDefaults::SNAPSHOT_PATH_NAME),
            $append,
        ]);
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
            throw new \RuntimeException('The web server "' . $instance->webServer->server_id_text . '" does not have a mount defined.');
        }

        return $_mount->getFilesystem($path, $tag, $options);
    }

    /**
     * Returns the relative root directory of this instance's storage
     *
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string                                            $tag
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function getRootStorageMount(Instance $instance, $tag = null)
    {
        return $this->mount($instance,
            $this->getRootStoragePath($instance),
            $tag ?: 'storage-root:' . $instance->instance_id_text);
    }

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string                                            $tag
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function getStorageMount(Instance $instance, $tag = null)
    {
        return $this->mount($instance,
            $this->getStoragePath($instance),
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
        return $this->mount($instance,
            $this->getSnapshotPath($instance),
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
        return $this->mount($instance,
            $this->getPrivatePath($instance),
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
        return $this->mount($instance,
            $this->getOwnerPrivatePath($instance),
            $tag ?: 'owner-private-storage:' . $instance->instance_id_text);
    }

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string|null                                       $append Optional appendage to path
     *
     * @return string
     * @throws \DreamFactory\Enterprise\Common\Exceptions\DiskException
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
     * @param string $path
     * @param bool   $addSlash     If true (default), a leading slash is added
     * @param bool   $trimTrailing If true, any trailing slashes are removed
     *
     * @return string
     */
    protected function cleanPath($path, $addSlash = true, $trimTrailing = false)
    {
        $path = $path ? ($addSlash ? DIRECTORY_SEPARATOR : null) . ltrim($path, ' ' . DIRECTORY_SEPARATOR) : $path;

        return $trimTrailing ? rtrim($path, DIRECTORY_SEPARATOR) : $path;
    }
}