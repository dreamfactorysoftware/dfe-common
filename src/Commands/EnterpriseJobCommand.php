<?php
namespace DreamFactory\Enterprise\Common\Commands;

use DreamFactory\Enterprise\Common\Traits\StaticComponentLookup;
use DreamFactory\Enterprise\Services\Enums\ServerTypes;
use Illuminate\Contracts\Queue\ShouldBeQueued;

/**
 * A base class for all DFE "job" type commands (non-console)
 */
abstract class EnterpriseJobCommand extends JobCommand implements ShouldBeQueued
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string|bool The queue upon which to push myself. Set to false to not use queuing
     */
    const JOB_QUEUE = 'enterprise';

    //******************************************************************************
    //* Traits
    //******************************************************************************

    use StaticComponentLookup;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string|int The id of the cluster
     */
    protected $_clusterId;
    /**
     * @type string|int The id of the web server
     */
    protected $_serverId;
    /**
     * @type int An OwnerTypes enum
     */
    protected $_serverType;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string|int $clusterId
     * @param string|int $serverId
     * @param int        $serverType
     */
    public function __construct( $clusterId = null, $serverId = null, $serverType = null )
    {
        $this->_clusterId = $clusterId ?: config( 'dfe.provisioning.default-cluster-id' );
        $this->_serverId = $serverId ?: config( 'dfe.provisioning.default-db-server-id' );
    }

    /**
     * @return mixed
     */
    public function getClusterId()
    {
        return $this->_clusterId;
    }

    /**
     * @param mixed $clusterId
     *
     * @return EnterpriseJobCommand
     */
    public function setClusterId( $clusterId )
    {
        $_cluster = static::_lookupCluster( $clusterId );
        $this->_clusterId = $_cluster->cluster_id_text;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getServerId()
    {
        return $this->_serverId;
    }

    /**
     * @param mixed $serverId
     *
     * @return EnterpriseJobCommand
     */
    public function setServerId( $serverId )
    {
        $_server = static::_lookupServer( $serverId );
        $this->_serverId = $_server->server_id_text;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getServerType()
    {
        return $this->_serverType;
    }

    /**
     * @param mixed $serverType Defaults to "WEB"
     *
     * @return EnterpriseJobCommand
     */
    public function setServerType( $serverType = ServerTypes::WEB )
    {
        $this->_serverType = ServerTypes::contains( $serverType, true ) ?: ServerTypes::WEB;
    }

}
