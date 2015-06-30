<?php
namespace DreamFactory\Enterprise\Common\Jobs;

use DreamFactory\Enterprise\Common\Jobs\BaseJob;
use DreamFactory\Enterprise\Common\Traits\StaticComponentLookup;
use DreamFactory\Enterprise\Database\Enums\OwnerTypes;
use DreamFactory\Enterprise\Services\Contracts\EnterpriseJob;
use DreamFactory\Enterprise\Services\Enums\ServerTypes;

/**
 * A base class for all DFE non-instance "job" type commands (non-console)
 */
abstract class BaseEnterpriseJob extends BaseJob implements EnterpriseJob
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
    protected $clusterId;
    /**
     * @type string|int The id of the web server
     */
    protected $serverId;
    /**
     * @type int An OwnerTypes enum
     */
    protected $serverType;
    /**
     * @type int
     */
    protected $ownerId;
    /**
     * @type int
     */
    protected $ownerType;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string|int $clusterId
     * @param string|int $serverId
     * @param int        $serverType
     */
    public function __construct($clusterId = null, $serverId = null, $serverType = null)
    {
        $this->clusterId = $clusterId ?: config('dfe.provisioning.default-cluster-id');
        $this->serverId = $serverId ?: config('dfe.provisioning.default-db-server-id');
    }

    /**
     * @return mixed
     */
    public function getClusterId()
    {
        return $this->clusterId;
    }

    /**
     * @param mixed $clusterId
     *
     * @return $this
     */
    public function setClusterId($clusterId)
    {
        $_cluster = static::_lookupCluster($clusterId);
        $this->clusterId = $_cluster->cluster_id_text;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getServerId()
    {
        return $this->serverId;
    }

    /**
     * @param mixed $serverId
     *
     * @return $this
     */
    public function setServerId($serverId)
    {
        $_server = static::_lookupServer($serverId);
        $this->serverId = $_server->server_id_text;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getServerType()
    {
        return $this->serverType;
    }

    /**
     * @param mixed $serverType Defaults to "WEB"
     *
     * @return $this
     */
    public function setServerType($serverType = ServerTypes::WEB)
    {
        $this->serverType = ServerTypes::contains($serverType, true) ?: ServerTypes::WEB;
    }

    /**
     * @return int
     */
    public function getOwnerId()
    {
        return $this->ownerId;
    }

    /**
     * @param int $ownerId
     *
     * @return $this
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    /**
     * @return int
     */
    public function getOwnerType()
    {
        return $this->ownerType;
    }

    /**
     * @param int $ownerType
     *
     * @return $this
     */
    public function setOwnerType($ownerType = OwnerTypes::USER)
    {
        $this->ownerType =
            (is_numeric($ownerType) && OwnerTypes::contains($ownerType))
                ? $ownerType
                : OwnerTypes::defines($ownerType, true);

        return $this;
    }

    /** @inheritdoc */
    public function getCluster($clusterId = null)
    {
        return static::_lookupCluster($clusterId ?: $this->clusterId);
    }

    /** @inheritdoc */
    public function getServer($serverId = null)
    {
        return static::_lookupServer($serverId ?: $this->serverId);
    }

}