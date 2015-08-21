<?php namespace DreamFactory\Enterprise\Common\Services;

use DreamFactory\Enterprise\Common\Enums\EnterpriseDefaults;
use DreamFactory\Enterprise\Database\Enums\GuestLocations;
use DreamFactory\Enterprise\Database\Models\Instance;
use DreamFactory\Library\Utility\Disk;
use DreamFactory\Library\Utility\Exceptions\DiskException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

/**
 * Core instance storage service
 *
 * This service maps guest location storage mounts to user/instance-specific paths resulting in absolute paths
 * (path methods) or Flysystems (mount methods). Absolute paths are NEVER returned from this service.
 */
class InstanceStorageService extends BaseService
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type int The location of this storage service
     */
    protected $guestLocation;
    /**
     * @type string The base value used to hash and partition storage. If null, stand-alone instance is assumed.
     */
    protected $hashBase;
    /**
     * @type Instance The current instance
     */
    protected $instance;
    /**
     * @type array The calculated storage map
     */
    protected $map;
    /**
     * @type string
     */
    protected $privatePathName;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param \Illuminate\Contracts\Foundation\Application|null $app           The app
     * @param int                                               $guestLocation The guest location to service
     * @param string|null                                       $hashBase      Value used for partition hash (instance|user->storage_id_text)
     */
    public function __construct($app = null, $guestLocation = GuestLocations::DFE_CLUSTER, $hashBase = null)
    {
        parent::__construct($app);

        null !== $guestLocation && $this->setGuestLocation($guestLocation);
        null !== $hashBase && $this->hashBase = $hashBase;
    }

    /**
     * Init
     */
    public function boot()
    {
        parent::boot();

        //  Ensure guest location
        null === $this->guestLocation && $this->setGuestLocation(GuestLocations::DFE_CLUSTER);

        //  and private path name
        null === $this->privatePathName &&
        $this->privatePathName = trim(config('provisioning.private-path-name', EnterpriseDefaults::PRIVATE_PATH_NAME),
            DIRECTORY_SEPARATOR . ' ');
    }

    /**
     * Returns the absolute path to the trash
     *
     * @param string|array|null $append
     * @param bool              $create If true, create when non-existent
     *
     * @return string
     */
    public function getTrashPath($append = null, $create = true)
    {
        static $_path;

        return $_path ?: $_path = Disk::path([config('snapshot.trash-path'), $append], $create);
    }

    /**
     * Returns the *RELATIVE* TOP/ROOT/MOUNT (top/user level) storage path for current $hashBase.
     * If no $hashBase is set, the result of storage_path() method is returned.
     *
     * NOTE: MAKE SURE TO SET $this->hashBase BEFORE YOU CALL THIS METHOD!
     *
     * @param string|array|null $append
     *
     * @return string
     */
    public function getStorageRootPath($append = null)
    {
        static $_cache = [];

        $_ck = hash(EnterpriseDefaults::DEFAULT_SIGNATURE_METHOD,
            implode('.', ['user-storage-path', $this->hashBase, $append]));

        //  Map out the path
        if (null === ($_path = array_get($_cache, $_ck))) {
            if (empty($this->hashBase) || false === $this->buildStorageMap()) {
                $_path = storage_path($append);
            } else {
                $_path = Disk::segment([$this->resolvePathFromMap(), $append], true);
            }

            $_cache[$_ck] = $_path;
        }

        return $_path;
    }

    /*------------------------------------------------------------------------------*/
    /* Standard Paths                                                               */
    /*------------------------------------------------------------------------------*/

    /**
     * Given an instance, return an absolute path to "/storage"
     *
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance The instance in question
     * @param string|null                                       $append   Optional path to append
     * @param bool                                              $create   If true, directory will be created
     *
     * @return string
     */
    public function getStoragePath(Instance $instance, $append = null, $create = false)
    {
        $this->buildStorageMap($instance->user->storage_id_text);

        return Disk::path([$this->getStorageRootPath(), $instance->instance_id_text, $append], $create);
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
        $this->buildStorageMap($instance->user->storage_id_text);

        return Disk::path([$this->getStoragePath($instance), $this->privatePathName, $append], $create);
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
    public function getOwnerPrivatePath(Instance $instance, $append = null, $create = false)
    {
        $this->buildStorageMap($instance->user->storage_id_text);

        return Disk::path([$this->getStorageRootPath(), $this->privatePathName, $append], $create);
    }

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string|null                                       $append Optional path to append
     * @param bool                                              $create
     *
     * @return string
     */
    public function getSnapshotPath(Instance $instance, $append = null, $create = false)
    {
        return Disk::path([
            $this->getOwnerPrivatePath($instance),
            config('provisioning.snapshot-path-name', EnterpriseDefaults::SNAPSHOT_PATH_NAME),
            $append,
        ],
            $create);
    }

    /**
     * Returns a path where you can write instance-specific temporary data
     *
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string|null                                       $append Optional appendage to path
     *
     * @return string
     * @throws DiskException
     */
    public function getWorkPath(Instance $instance, $append = null)
    {
        $this->buildStorageMap($instance->user->storage_id_text);

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

    /*------------------------------------------------------------------------------*/
    /* Mounts                                                                       */
    /*------------------------------------------------------------------------------*/

    /**
     * @param string|array|null $append
     * @param bool              $create If true, create when non-existent
     *
     * @return string
     */
    public function getTrashMount($append = null, $create = true)
    {
        static $_mount;

        return $_mount ?: $_mount = new Filesystem(new Local($this->getTrashPath($append, $create)));
    }

    /**
     * Returns the instance's storage area as a filesystem
     *
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string|null                                       $tag
     *
     * @return \League\Flysystem\Filesystem
     */
    public function getStorageRootMount(Instance $instance, $tag = null)
    {
        return $this->mount($instance,
            $this->getStorageRootPath(),
            $tag ?: 'storage-root:' . $instance->instance_id_text);
    }

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string                                            $tag
     *
     * @return \League\Flysystem\Filesystem
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
     * @return \League\Flysystem\Filesystem
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
     * @return \League\Flysystem\Filesystem
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
     * @return \League\Flysystem\Filesystem
     */
    public function getOwnerPrivateStorageMount(Instance $instance, $tag = null)
    {
        return $this->mount($instance,
            $this->getOwnerPrivatePath($instance),
            $tag ?: 'owner-private-storage:' . $instance->instance_id_text);
    }

    /*------------------------------------------------------------------------------*/
    /* Service methods                                                               */
    /*------------------------------------------------------------------------------*/

    /**
     * Returns the proper storage zone for this location
     *
     * @return string
     */
    protected function getStorageZone()
    {
        static $_zone;

        if (empty($_zone)) {
            switch (config('provisioning.storage-zone-type')) {
                case 'dynamic':
                    switch ($this->guestLocation) {
                        case GuestLocations::AMAZON_EC2:
                        case GuestLocations::DFE_CLUSTER:
                            if (file_exists('/usr/bin/ec2metadata')) {
                                $_zone = str_replace('availability-zone: ',
                                    null,
                                    `/usr/bin/ec2metadata | grep zone`);
                            }
                            break;
                    }
                    break;

                case 'static':
                    $_zone = config('provisioning.static-zone-name');
                    break;
            }
        }

        if (empty($_zone)) {
            throw new \RuntimeException('Storage zone or type invalid. Cannot provision storage.');
        }

        return $_zone;
    }

    /**
     * Returns a Flysystem filesystem object mapped to the instance's path
     *
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param string                                            $path
     * @param string                                            $tag
     * @param array                                             $options
     *
     * @return \League\Flysystem\Filesystem
     */
    protected function mount($instance, $path, $tag = null, $options = [])
    {
        if (!$instance->webServer) {
            throw new \InvalidArgumentException('No configured web server for instance.');
        }

        $_mount = $instance->webServer->mount;

        empty($this->hashBase) && $this->buildStorageMap($instance->user->storage_id_text);

        if (!$_mount) {
            throw new \RuntimeException('The web server "' .
                $instance->webServer->server_id_text .
                '" does not have a mount defined.');
        }

        return $_mount->getFilesystem($path, $tag, $options);
    }

    /**
     * @param string|null $hashBase The $hashBase to set (via setHashBase()) and use
     *
     * @return array|boolean The storage map built, or false if this is a local instance
     */
    public function buildStorageMap($hashBase = null)
    {
        static $_mapCache = [];

        $_hashBase = $hashBase ?: $this->hashBase;

        if (empty($_hashBase) || GuestLocations::LOCAL == $this->guestLocation) {
            logger('No hash-base set in storage service. Stand-alone instance implied.');
            $this->map = [
                'zone'      => null,
                'partition' => null,
                'root-hash' => null,
            ];

            return false;
        }

        if (null === ($_map = array_get($_mapCache, $_hashBase))) {
            empty($this->hashBase) && $this->hashBase = $_hashBase;
            $_rootHash = hash(config('dfe.signature-method', EnterpriseDefaults::SIGNATURE_METHOD), $_hashBase);

            $_map = [
                'zone'      => $this->getStorageZone(),
                'partition' => substr($_rootHash, 0, 2),
                'root-hash' => $_rootHash,
            ];

            $_mapCache[$_hashBase] = $_map;
        }

        return $this->map = $_map;
    }

    /**
     * @param bool   $leading   If true, a leading $separator is pre-pended to result
     * @param string $separator The separator between map parts
     *
     * @return array
     */
    protected function resolvePathFromMap($leading = true, $separator = DIRECTORY_SEPARATOR)
    {
        return Disk::segment(array_only($this->map, ['zone', 'partition', 'root-hash']), $leading, $separator);
    }

    /*------------------------------------------------------------------------------*/
    /* Properties                                                                   */
    /*------------------------------------------------------------------------------*/

    /**
     * @param int $guestLocation
     *
     * @return InstanceStorageService
     */
    public function setGuestLocation($guestLocation)
    {
        if (!is_numeric($guestLocation)) {
            $guestLocation = GuestLocations::resolve($guestLocation, true);
        }

        $this->guestLocation = $guestLocation;

        return $this;
    }

    /**
     * @param string $hashBase
     *
     * @return InstanceStorageService
     */
    public function setHashBase($hashBase)
    {
        $this->hashBase = $hashBase;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrivatePathName()
    {
        return $this->privatePathName;
    }

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     *
     * @return $this
     */
    public function setInstance(Instance $instance)
    {
        $this->instance = $instance;
        $this->buildStorageMap($instance->user->storage_id_text);

        return $this;
    }
}