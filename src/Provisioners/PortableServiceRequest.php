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
     * @param Instance    $instance
     * @param string      $from
     * @param string|null $workPath
     *
     * @return static
     */
    public static function makeImport($instance, $from, $workPath = null)
    {
        $_request = new static($instance, ['work-path' => $workPath]);

        return $_request->setTarget($from);
    }

    /**
     * @param Instance        $instance
     * @param Filesystem|null $to
     * @param string|null     $workPath
     *
     * @return static
     */
    public static function makeExport($instance, $to = null, $workPath = null)
    {
        $_request = new static($instance, ['work-path' => $workPath]);

        return $_request->setTarget($to);
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
