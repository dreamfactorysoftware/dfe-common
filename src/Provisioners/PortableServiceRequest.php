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
     * @param Instance $instance
     * @param array    $items
     */
    public function __construct(Instance $instance, $items = [])
    {
        parent::__construct($items);

        $this->put('instance', $instance);
        $this->put('instance-id', $instance->id);
    }

    /**
     * @param Instance|string|int $instance
     * @param string              $from
     * @param array               $items Additional items to put in the request
     *
     * @return static
     */
    public static function makeImport($instance, $from, $items = [])
    {
        !($instance instanceof Instance) && $instance = static::_locateInstance($instance);

        return new static($instance, array_merge($items, ['target' => $from,]));
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
