<?php namespace DreamFactory\Enterprise\Common\Provisioners;

use DreamFactory\Enterprise\Database\Models\Instance;
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
     * @param Instance|string|null $instance
     * @param array                $items
     */
    public function __construct($instance = null, $items = [])
    {
        if (!empty($instance)) {
            is_string($instance) && $items['instance-id'] = $instance;
            !empty($instance) && $instance instanceof Instance && $items['instance'] = $instance;
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
     * @param Instance|string|int $instance
     * @param Filesystem|null     $to
     * @param array               $items Additional items to put in the request
     *
     * @return static
     */
    public static function makeExport($instance, $to = null, $items = [])
    {
        if (empty($instance)) {
            throw new \InvalidArgumentException('Instance "' . $instance . '"  is invalid.');
        }

        !($instance instanceof Instance) && $instance = static::_locateInstance($instance);

        return new static($instance, array_merge($items, ['target' => $to,]));
    }

    /**
     * Retrieves the target of the operation
     *
     * @param string|mixed|null $default
     *
     * @return mixed
     */
    public function getTarget($default = null)
    {
        return $this->get('target', $default);
    }

    /**
     * @param mixed $target
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
     * @return Instance|null
     */
    public function getInstance()
    {
        return $this->get('instance');
    }
}