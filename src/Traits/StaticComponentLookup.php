<?php
namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Database\Enums\ServerTypes;
use DreamFactory\Enterprise\Database\Models\AppKey;
use DreamFactory\Enterprise\Database\Models\Cluster;
use DreamFactory\Enterprise\Database\Models\ClusterServer;
use DreamFactory\Enterprise\Database\Models\Instance;
use DreamFactory\Enterprise\Database\Models\InstanceServer;
use DreamFactory\Enterprise\Database\Models\Mount;
use DreamFactory\Enterprise\Database\Models\Server;
use DreamFactory\Enterprise\Database\Models\ServiceUser;
use DreamFactory\Enterprise\Database\Models\User;
use DreamFactory\Enterprise\Database\Models\UserRole;
use DreamFactory\Library\Utility\IfSet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

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
     *
     * @param int $ownerId
     * @param int $ownerType
     *
     * @return AppKey
     */
    protected static function _lookupAppKey( $ownerId, $ownerType )
    {
        return AppKey::mine( $ownerId, $ownerType );
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
        $_servers = [];

        foreach ( ServerTypes::getDefinedConstants() as $_name => $_value )
        {
            $_servers[$_value] = [
                '.id'   => null,
                '.ids'  => [],
                '.name' => $_name,
                'data'  => [],
            ];
        }

        /**
         * @type int    $_type
         * @type Server $_server
         */
        foreach ( $_rows as $_type => $_server )
        {
            if ( !isset( $_servers[$_server->server_type_id] ) )
            {
                continue;
            }

            $_servers[$_server->server_type_id]['data'][$_server->server_id_text] = $_server->toArray();
            $_servers[$_server->server_type_id]['.ids'][] = $_server->id;
        }

        //  Set the single id for quick lookups
        foreach ( $_servers as $_type => $_group )
        {
            if ( null !== IfSet::get( $_group, '.id' ) )
            {
                continue;
            }

            if ( null !== ( $_list = IfSet::get( $_group, '.ids' ) ) )
            {
                if ( !empty( $_list ) && is_array( $_list ) )
                {
                    $_servers[$_type]['.id'] = $_list[0];
                    continue;
                }
            }

            if ( null !== ( $_list = IfSet::get( $_group, 'data' ) ) )
            {
                if ( !empty( $_list ) && is_array( $_list ) )
                {
                    foreach ( $_list as $_item )
                    {
                        if ( isset( $_item['id'] ) )
                        {
                            $_servers[$_type['.id']] = $_item['id'];
                            continue;
                        }
                    }
                }
            }
        }

        Log::debug( 'I made this: ' . print_r( $_servers, true ) );

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