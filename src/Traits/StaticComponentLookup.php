<?php
namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Services\Enums\ServerTypes;
use DreamFactory\Library\Fabric\Database\Models\Deploy\Cluster;
use DreamFactory\Library\Fabric\Database\Models\Deploy\ClusterServer;
use DreamFactory\Library\Fabric\Database\Models\Deploy\Instance;
use DreamFactory\Library\Fabric\Database\Models\Deploy\InstanceServer;
use DreamFactory\Library\Fabric\Database\Models\Deploy\Mount;
use DreamFactory\Library\Fabric\Database\Models\Deploy\Server;
use DreamFactory\Library\Fabric\Database\Models\Deploy\ServiceUser;
use DreamFactory\Library\Fabric\Database\Models\Deploy\User;
use DreamFactory\Library\Fabric\Database\Models\Deploy\UserRole;
use Illuminate\Support\Collection;

/**
 * A trait for looking up various enterprise components statically
 */
trait StaticComponentLookup
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * @param string|int $clusterId
     *
     * @return Cluster
     */
    protected static function _lookupCluster( $clusterId )
    {
        return Cluster::byNameOrId( $clusterId )->firstOrFail();
    }

    /**
     * @param int|string $serverId
     *
     * @return Server
     */
    protected static function _lookupServer( $serverId )
    {
        return Server::byNameOrId( $serverId )->firstOrFail();
    }

    /**
     * @param int|string $instanceId
     *
     * @return Instance
     */
    protected static function _lookupInstance( $instanceId )
    {
        return Instance::byNameOrId( $instanceId )->firstOrFail();
    }

    /**
     * @param int|string $mountId
     *
     * @return Instance
     */
    protected static function _lookupMount( $mountId )
    {
        return Mount::byNameOrId( $mountId )->firstOrFail();
    }

    /**
     * @param int $userId
     *
     * @return User
     */
    protected static function _lookupUser( $userId )
    {
        return User::findOrFail( $userId );
    }

    /**
     * @param int $serviceUserId
     *
     * @return User
     */
    protected static function _lookupServiceUser( $serviceUserId )
    {
        return ServiceUser::findOrFail( $serviceUserId );
    }

    /**
     * Returns all servers registered on $clusterId
     *
     * @param int $clusterId
     *
     * @return Collection
     */
    protected static function _lookupClusterServers( $clusterId )
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
     * Returns all instances registered on $serverId
     *
     * @param int $serverId
     *
     * @return Collection
     */
    protected static function _lookupServerInstances( $serverId )
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
    protected static function _lookupUserRoles( $userId )
    {
        return UserRole::join( 'role_t', 'id', '=', 'role_id' )
            ->where( 'user_id', '=', $userId )
            ->orderBy( 'role_t.role_name_text' )
            ->get( ['role_t.*'] );
    }
}