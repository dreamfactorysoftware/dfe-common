<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Common\Enums\ServerTypes;
use DreamFactory\Enterprise\Database\Enums\OwnerTypes;
use DreamFactory\Enterprise\Database\Models\AppKey;
use DreamFactory\Enterprise\Database\Models\Cluster;
use DreamFactory\Enterprise\Database\Models\ClusterServer;
use DreamFactory\Enterprise\Database\Models\Instance;
use DreamFactory\Enterprise\Database\Models\InstanceArchive;
use DreamFactory\Enterprise\Database\Models\InstanceServer;
use DreamFactory\Enterprise\Database\Models\Mount;
use DreamFactory\Enterprise\Database\Models\Server;
use DreamFactory\Enterprise\Database\Models\ServiceUser;
use DreamFactory\Enterprise\Database\Models\Snapshot;
use DreamFactory\Enterprise\Database\Models\User;
use DreamFactory\Enterprise\Database\Models\UserRole;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * A trait for looking up various enterprise components statically
 */
trait StaticEntityLookup
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     *
     * @param int $ownerId
     * @param int $ownerType
     *
     * @return \DreamFactory\Enterprise\Database\Models\AppKey
     */
    protected static function findAppKey($ownerId, $ownerType)
    {
        return AppKey::mine($ownerId, $ownerType);
    }

    /**
     * @param int|string|\DreamFactory\Enterprise\Database\Models\User $userId
     *
     * @return \DreamFactory\Enterprise\Database\Models\User
     */
    protected static function findUser($userId)
    {
        return ($userId instanceof User) ? $userId : User::findOrFail($userId);
    }

    /**
     * @param int|string|\DreamFactory\Enterprise\Database\Models\ServiceUser $serviceUserId
     *
     * @return \DreamFactory\Enterprise\Database\Models\User
     */
    protected static function findServiceUser($serviceUserId)
    {
        return ($serviceUserId instanceof ServiceUser) ? $serviceUserId : ServiceUser::findOrFail($serviceUserId);
    }

    /**
     * @param string|int|\DreamFactory\Enterprise\Database\Models\Cluster $clusterId
     *
     * @return \DreamFactory\Enterprise\Database\Models\Cluster
     */
    protected static function findCluster($clusterId)
    {
        return ($clusterId instanceof Cluster) ? $clusterId : Cluster::byNameOrId($clusterId)->firstOrFail();
    }

    /**
     * @param int|string|\DreamFactory\Enterprise\Database\Models\Server $serverId
     *
     * @return \DreamFactory\Enterprise\Database\Models\Server
     */
    protected static function findServer($serverId)
    {
        return ($serverId instanceof Server) ? $serverId : Server::byNameOrId($serverId)->firstOrFail();
    }

    /**
     * @param int|string|\DreamFactory\Enterprise\Database\Models\Instance $instanceId
     *
     * @return \DreamFactory\Enterprise\Database\Models\Instance
     */
    protected static function findInstance($instanceId)
    {
        return ($instanceId instanceof Instance) ? $instanceId : Instance::with(['user', 'guest'])->byNameOrId($instanceId)->firstOrFail();
    }

    /**
     * @param int|string|\DreamFactory\Enterprise\Database\Models\InstanceArchive $instanceId
     *
     * @return \DreamFactory\Enterprise\Database\Models\InstanceArchive
     */
    protected static function findArchivedInstance($instanceId)
    {
        return ($instanceId instanceof InstanceArchive) ? $instanceId : InstanceArchive::with(['user', 'guest'])->byNameOrId($instanceId)->firstOrFail();
    }

    /**
     * Looks first in instance_t, then in instance_arch_t. If nothing found returns null.
     *
     * @param int|string|\DreamFactory\Enterprise\Database\Models\Instance $instanceId
     *
     * @return \DreamFactory\Enterprise\Database\Models\Instance|null
     */
    protected static function locateInstance($instanceId)
    {
        try {
            return static::findInstance($instanceId);
        } catch (ModelNotFoundException $_ex) {
            try {
                return static::findArchivedInstance($instanceId);
            } catch (ModelNotFoundException $_ex) {
                return null;
            }
        }
    }

    /**
     * @param string|int|\DreamFactory\Enterprise\Database\Models\Snapshot $snapshotId
     *
     * @return \DreamFactory\Enterprise\Database\Models\Snapshot
     */
    protected static function findSnapshot($snapshotId)
    {
        return ($snapshotId instanceof Snapshot) ? $snapshotId : Snapshot::bySnapshotId($snapshotId)->with(['user', 'routeHash'])->firstOrFail();
    }

    /**
     * @param int|string|\DreamFactory\Enterprise\Database\Models\Mount $mountId
     *
     * @return \DreamFactory\Enterprise\Database\Models\Mount
     */
    protected static function findMount($mountId)
    {
        return ($mountId instanceof Mount) ? $mountId : Mount::byNameOrId($mountId)->firstOrFail();
    }

    /**
     * Returns all servers registered on $clusterId
     *
     * @param int|string|\DreamFactory\Enterprise\Database\Models\Cluster $clusterId
     *
     * @return array
     */
    protected static function findClusterServers($clusterId)
    {
        //  Organize by type
        $_response = [
            ServerTypes::APP => [],
            ServerTypes::DB  => [],
            ServerTypes::WEB => [],
        ];

        /** @type Server $_server */
        foreach (static::findCluster($clusterId)->assignedServers() as $_assignment) {
            if (null !== ($_server = $_assignment->server)) {
                $_response[$_server->server_type_id][$_server->server_id_text] = $_server;
            }
        }

        return $_response;
    }

    /**
     * Returns all instances registered on $serverId
     *
     * @param int $serverId
     *
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    protected static function findServerInstances($serverId)
    {
        return InstanceServer::join('instance_t', 'id', '=', 'instance_id')
            ->where('server_id', '=', $serverId)
            ->orderBy('instance_t.instance_id_text')
            ->get(['instance_t.*']);
    }

    /**
     * Returns all instances managed by $clusterId
     *
     * @param \DreamFactory\Enterprise\Database\Models\Cluster|int $clusterId
     * @param array                                                $columns The columns to retrieve
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    protected static function findClusterInstances($clusterId, $columns = ['*'])
    {
        return Instance::where('cluster_id', static::findCluster($clusterId)->id)->orderBy('instance_id_text')->get($columns);
    }

    /**
     * Returns all assigned roles for a user
     *
     * @param int $userId
     *
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    protected static function findUserRoles($userId)
    {
        return UserRole::join('role_t', 'id', '=', 'role_id')->where('user_id', '=', $userId)->orderBy('role_t.role_name_text')->get(['role_t.*']);
    }

    /**
     * Returns an collection of clusters to which $serverId is assigned
     *
     * @param string|int $serverId
     *
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    protected static function findServerClusters($serverId)
    {
        return ClusterServer::with(['server', 'cluster'])->where('server_id', '=', $serverId)->get();
    }

    /**
     * @param int $id
     * @param int $type
     *
     * @return \DreamFactory\Enterprise\Database\Contracts\OwnedEntity
     */
    protected static function findOwner($id, $type = OwnerTypes::USER)
    {
        try {
            $_owner = OwnerTypes::getOwner($id, $type);
        } catch (Exception $_ex) {
            is_string($id) && $_owner = User::byEmail($id)->first() && $type = OwnerTypes::USER;
        }
        finally {
            if (empty($_owner)) {
                throw new ModelNotFoundException('The owner-id "' . $id . '" could not be found.');
            }
        }

        return $_owner;
    }
}
