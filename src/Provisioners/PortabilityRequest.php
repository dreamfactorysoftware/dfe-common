<?php namespace DreamFactory\Enterprise\Common\Provisioners;

use DreamFactory\Enterprise\Common\Traits\EntityLookup;
use DreamFactory\Enterprise\Common\Traits\HasResults;
use DreamFactory\Enterprise\Database\Models\Instance;
use League\Flysystem\Filesystem;

/**
 * A dumb container for portability jobs
 */
class PortabilityRequest
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use HasResults, EntityLookup;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string|int The instance id
     */
    protected $instanceId;
    /**
     * @type Instance The target instance
     */
    protected $instance;
    /**
     * @type string A working directory, if any
     */
    protected $workPath;
    /**
     * @type mixed A source, if any
     */
    protected $from;
    /**
     * @type mixed A target, if any
     */
    protected $to;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string|int  $instanceId
     * @param string|null $workPath
     */
    public function __construct($instanceId, $workPath = null)
    {
        $this->setInstanceId($instanceId);
        $this->workPath = $workPath;
    }

    /**
     * @param Instance    $instance
     * @param string      $from
     * @param string|null $workPath
     *
     * @return static
     */
    public static function createImport($instance, $from, $workPath = null)
    {
        $_request = new static($instance, $workPath);
        $_request->setFrom($from);

        return $_request;
    }

    /**
     * @param Instance        $instance
     * @param Filesystem|null $to
     * @param string|null     $workPath
     *
     * @return static
     */
    public static function createExport($instance, $to = null, $workPath = null)
    {
        $_request = new static($instance, $workPath);
        $_request->setTo($to);

        return $_request;
    }

    /**
     * @return Instance
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @return string
     */
    public function getWorkPath()
    {
        return $this->workPath;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param mixed $from
     *
     * @return PortabilityRequest
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param mixed $to
     *
     * @return PortabilityRequest
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return int|string
     */
    public function getInstanceId()
    {
        return $this->instanceId;
    }

    /**
     * @param int|string $instanceId
     *
     * @return PortabilityRequest
     */
    protected function setInstanceId($instanceId)
    {
        $this->instance = $this->_findInstance($instanceId);
        $this->instanceId = $this->instance->instance_id_text;

        return $this;
    }
}
