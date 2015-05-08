<?php
namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Services\Enums\ServerTypes;
use DreamFactory\Library\Fabric\Database\Enums\OwnerTypes;
use DreamFactory\Library\Fabric\Database\Models\Deploy\Cluster;
use DreamFactory\Library\Fabric\Database\Models\Deploy\ClusterServer;
use DreamFactory\Library\Fabric\Database\Models\Deploy\Instance;
use DreamFactory\Library\Fabric\Database\Models\Deploy\InstanceServer;
use DreamFactory\Library\Fabric\Database\Models\Deploy\Server;
use DreamFactory\Library\Fabric\Database\Models\Deploy\User;
use DreamFactory\Library\Fabric\Database\Models\Deploy\UserRole;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
    protected function _findCluster( $clusterId )
    {
        return Cluster::byNameOrId( $clusterId )->firstOrFail();
    }

    /**
     * @param int|string $serverId
     *
     * @return Server
     */
    protected function _findServer( $serverId )
    {
        return Server::byNameOrId( $serverId )->firstOrFail();
    }

    /**
     * @param int|string $instanceId
     *
     * @return Instance
     */
    protected function _findInstance( $instanceId )
    {
        return Instance::with( ['user', 'guest'] )->byNameOrId( $instanceId )->firstOrFail();
    }

    /**
     * @param int $userId
     *
     * @return User
     */
    protected function _findUser( $userId )
    {
        return User::where( 'id', '=', $userId )->findOrfail( $userId );
    }

    /**
     * Returns all servers registered on $clusterId
     *
     * @param int $clusterId
     *
     * @return Collection
     */
    protected function _clusterServers( $clusterId )
    {
        $_rows = ClusterServer::join( 'server_t', 'id', '=', 'server_id' )
            ->where( 'cluster_id', '=', $clusterId )
            ->get(
                [
                    'server_t.id',
                    'server_t.server_id_text',
                    'server_t.server_type_id',
                    'cluster_server_asgn_t.cluster_id'
                ]
            );

        //  Organize by type
        $_servers = [
            ServerTypes::APP => [],
            ServerTypes::DB  => [],
            ServerTypes::WEB => [],
        ];

        foreach ( $_rows as $_server )
        {
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
    protected function _serverClusters( $serverId )
    {
        return ClusterServer::where( 'server_id', '=', $serverId )
            ->get();
    }

    /**
     * Returns all instances registered on $serverId
     *
     * @param int $serverId
     *
     * @return Collection
     */
    protected function _serverInstances( $serverId )
    {
        return InstanceServer::join( 'instance_t', 'id', '=', 'instance_id' )
            ->where( 'server_id', '=', $serverId )
            ->orderBy( 'instance_t.instance_id_text' )
            ->get( ['instance_t.*'] );
    }

    /**
     * Returns all assigned roles for a user
     *
     * @param int $userId
     *
     * @return Collection
     */
    protected function _userRoles( $userId )
    {
        return UserRole::join( 'role_t', 'id', '=', 'role_id' )
            ->where( 'user_id', '=', $userId )
            ->orderBy( 'role_t.role_name_text' )
            ->get( ['role_t.*'] );
    }

    /**
     * @param int $id
     * @param int $type
     *
     * @return \DreamFactory\Library\Fabric\Database\Models\Deploy\Cluster|\DreamFactory\Library\Fabric\Database\Models\Deploy\Instance|\DreamFactory\Library\Fabric\Database\Models\Deploy\Server|\DreamFactory\Library\Fabric\Database\Models\Deploy\User
     */
    protected function _locateOwner( $id, $type = OwnerTypes::USER )
    {
        return OwnerTypes::getOwner($id,$type);
    }
}