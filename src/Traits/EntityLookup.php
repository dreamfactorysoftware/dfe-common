<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Common\Enums\ServerTypes;
use DreamFactory\Enterprise\Database\Enums\OwnerTypes;
use DreamFactory\Enterprise\Database\Models\Cluster;
use DreamFactory\Enterprise\Database\Models\ClusterServer;
use DreamFactory\Enterprise\Database\Models\Instance;
use DreamFactory\Enterprise\Database\Models\InstanceArchive;
use DreamFactory\Enterprise\Database\Models\InstanceServer;
use DreamFactory\Enterprise\Database\Models\Mount;
use DreamFactory\Enterprise\Database\Models\Server;
use DreamFactory\Enterprise\Database\Models\Snapshot;
use DreamFactory\Enterprise\Database\Models\User;
use DreamFactory\Enterprise\Database\Models\UserRole;
use Illuminate\Support\Collection;

/**
 * A trait for looking up various enterprise components
 */
trait EntityLookup
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * @param string|int $clusterId
     *
     * @return Cluster
     */
    protected function _findCluster($clusterId)
    {
        return Cluster::byNameOrId($clusterId)->firstOrFail();
    }

    /**
     * @param int|string $serverId
     *
     * @return Server
     */
    protected function _findServer($serverId)
    {
        return Server::byNameOrId($serverId)->firstOrFail();
    }

    /**
     * @param int|string $instanceId
     *
     * @return Instance
     */
    protected function _findInstance($instanceId)
    {
        return Instance::with(['user', 'guest'])->byNameOrId($instanceId)->firstOrFail();
    }

    /**
     * Looks first in instance_t, then in instance_arch_t. If nothing found returns null.
     *
     * @param int|string $instanceId
     *
     * @return Instance|null
     */
    protected function _locateInstance($instanceId)
    {
        if (null !== ($_instance = Instance::with(['user', 'guest'])->byNameOrId($instanceId)->first())) {
            return $_instance;
        }

        if (null !== ($_instance = InstanceArchive::with(['user', 'guest'])->byNameOrId($instanceId)->first())) {
            return $_instance;
        }

        return null;
    }

    /**
     * @param int $userId
     *
     * @return User
     */
    protected function _findUser($userId)
    {
        return User::where('id', '=', $userId)->findOrfail($userId);
    }

    /**
     * @param string $snapshotId
     *
     * @return Snapshot
     */
    protected function _findSnapshot($snapshotId)
    {
        return Snapshot::with(['user'])->where('snapshot_id_text', $snapshotId)->firstOrFail();
    }

    /**
     * Returns all servers registered on $clusterId
     *
     * @param int $clusterId
     *
     * @return Collection
     */
    protected function _clusterServers($clusterId)
    {
        $_cluster = $this->_findCluster($clusterId);

        $_rows = ClusterServer::join('server_t', 'id', '=', 'server_id')->where('cluster_id', '=', $_cluster->id)->get([
                    'server_t.id',
                    'server_t.server_id_text',
                    'server_t.server_type_id',
                    'cluster_server_asgn_t.cluster_id',
                ]);

        //  Organize by type
        $_servers = [
            ServerTypes::APP => [],
            ServerTypes::DB  => [],
            ServerTypes::WEB => [],
        ];

        /** @type Server $_server */
        foreach ($_rows as $_server) {
            $_servers[$_server->server_type_id][$_server->server_id_text] = $_server;
        }

        return $_servers;
    }

    /**
     * Returns all clusters registered on $serverId
     *
     * @param int $serverId
     *
     * @return Collection
     */
    protected function _serverClusters($serverId)
    {
        return ClusterServer::where('server_id', '=', $serverId)->get();
    }

    /**
     * Returns all instances registered on $serverId
     *
     * @param int $serverId
     *
     * @return Collection
     */
    protected function _serverInstances($serverId)
    {
        return InstanceServer::join('instance_t', 'id', '=', 'instance_id')
            ->where('server_id', '=', $serverId)
            ->orderBy('instance_t.instance_id_text')
            ->get(['instance_t.*']);
    }

    /**
     * Returns all assigned roles for a user
     *
     * @param int $userId
     *
     * @return Collection
     */
    protected function _userRoles($userId)
    {
        return UserRole::join('role_t', 'id', '=', 'role_id')
            ->where('user_id', '=', $userId)
            ->orderBy('role_t.role_name_text')
            ->get(['role_t.*']);
    }

    /**
     * @param int $id
     * @param int $type
     *
     * @return \DreamFactory\Enterprise\Database\Models\Cluster|\DreamFactory\Enterprise\Database\Models\Instance|\DreamFactory\Enterprise\Database\Models\Server|\DreamFactory\Enterprise\Database\Models\User
     */
    protected function _locateOwner($id, $type = OwnerTypes::USER)
    {
        return OwnerTypes::getOwner($id, $type);
    }

    /**
     * @param string|int $id
     *
     * @return Mount
     */
    protected function _findMount($id)
    {
        return Mount::byNameOrId($id)->firstOrFail();
    }
}