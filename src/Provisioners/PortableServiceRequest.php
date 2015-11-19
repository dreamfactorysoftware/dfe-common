<?php namespace DreamFactory\Enterprise\Common\Provisioners;

use League\Flysystem\Filesystem;

/**
 * A dumb container for portability jobs
 */
class PortableServiceRequest extends BaseRequest
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance|string|null $instance
     * @param array                                                         $items
     */
    public function __construct($instance = null, $items = [])
    {
        if (!empty($instance)) {
            if (is_string($instance)) {
                $items['instance-id'] = $instance;
            } else {
                $items['instance'] = $instance;
            }
        }

        parent::__construct($items);
    }

    /**
     * @param string $instanceId
     * @param string $from
     * @param array  $items Additional items to put in the request
     *
     * @return static
     */
    public static function makeImport($instanceId, $from, $items = [])
    {
        if (empty($instanceId)) {
            throw new \InvalidArgumentException('Instance "' . $instanceId . '"  is invalid.');
        }

        return new static($instanceId, array_merge($items, ['target' => $from,]));
    }

    /**
     * @param string|int      $instanceId The id of the instance to export
     * @param Filesystem|null $to         The destination of the export, otherwise to "snapshots"
     * @param array           $items      Additional items to put in the request
     *
     * @return static
     */
    public static function makeExport($instanceId, $to = null, $items = [])
    {
        if (empty($instanceId)) {
            throw new \InvalidArgumentException('Instance "' . $instanceId . '"  is invalid.');
        }

        return new static($instanceId, array_merge($items, ['target' => $to,]));
    }

    /**
     * Retrieves the target of the operation
     *
     * @param string|Filesystem|null $default
     *
     * @return string|Filesystem
     */
    public function getTarget($default = null)
    {
        return $this->get('target', $default);
    }

    /**
     * @param string|Filesystem $target
     *
     * @return $this
     */
    public function setTarget($target)
    {
        $this->put('target', $target);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getInstanceId()
    {
        return $this->get('instance-id');
    }

    /**
     * @return \DreamFactory\Enterprise\Database\Models\Instance|null
     */
    public function getInstance()
    {
        return $this->get('instance');
    }

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     *
     * @return $this
     */
    public function setInstance($instance)
    {
        return $this->put('instance', $instance);
    }
}
