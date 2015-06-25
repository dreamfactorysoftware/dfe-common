<?php namespace DreamFactory\Enterprise\Common\Support;

use DreamFactory\Enterprise\Common\Contracts\Custodial;
use DreamFactory\Enterprise\Common\Traits\Custodian;
use DreamFactory\Library\Utility\Exceptions\FileException;
use DreamFactory\Library\Utility\Json;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use League\Flysystem\Filesystem;

/**
 * Reads/writes a json file to a flysystem
 */
class FlyJson extends Json implements Arrayable, Jsonable, Custodial
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use Custodian;

    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type int The number of times to retry write operations (default is 3)
     */
    const STORAGE_OPERATION_RETRY_COUNT = 3;
    /**
     * @type int The number of times to retry write operations (default is 5s)
     */
    const STORAGE_OPERATION_RETRY_DELAY = 500000;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type bool If true, a copy of files to be overwritten will be made
     */
    protected $makeBackups = true;
    /**
     * @type Collection The contents of the file
     */
    protected $contents;
    /**
     * @type Filesystem The filesystem where the file lives
     */
    protected $filesystem;
    /**
     * @type string The name of the file
     */
    protected $filename;
    /**
     * @type array The template for the file, if any
     */
    protected $template = [];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param \League\Flysystem\Filesystem $filesystem  The filesystem on which this file lives
     * @param string                       $filename    The name of the file, relative to the root
     * @param bool                         $makeBackups If true, a copy of files to be overwritten will be made
     * @param array|object                 $contents    The contents to write to the file if being created
     */
    public function __construct(Filesystem $filesystem, $filename, $makeBackups = true, $contents = [])
    {
        $this->makeBackups = $makeBackups;
        $this->filesystem = $filesystem;
        $this->filename = $filename;

        $this->initialize($contents);
    }

    /**
     * @param array $contents The contents with which to initialize. Merged with existing manifest data
     *
     * @return $this
     */
    protected function initialize($contents = [])
    {
        $_existing = [];

        if (!is_array($contents) || empty($contents)) {
            $contents = [];
        }

        //  See if we have an existing file...
        try {
            $_existing = $this->read(false);
        } catch (\Exception $_ex) {
            //  Ignored but noted
            \Log::notice('error reading json file "' . $this->filename . '"');
        }

        return $this->reset($contents, $_existing);
    }

    /**
     * Completely resets the contents to $contents
     *
     * @param array $contents Any fresh contents to save
     * @param array $existing Any existing contents to merge with fresh content
     *
     * @return $this
     */
    protected function reset($contents = [], array $existing = [])
    {
        if (empty($existing) || !is_array($existing)) {
            $existing = [];
        }

        if (empty($contents) || !is_array($contents)) {
            $contents = [];
        }

        $_items = array_merge($existing, $contents);

        if (empty($this->template)) {
            $this->contents = new Collection($_items);
        } else {
            //  Load only keys that exist in the template
            $this->contents = new Collection();

            foreach ($_items as $_key => $_value) {
                if (array_key_exists($_key, $this->template)) {
                    $this->contents->put($_key, $_value);
                }
            }
        }

        return $this;
    }

    /**
     * Reads and loads the contents
     *
     * @param bool $reset If true (the default) the contents are reset and loaded from the file. If false, the
     *                    data is returned but the current contents are left undisturbed.
     *
     * @return array
     */
    public function read($reset = true)
    {
        $_contents = $this->doRead();

        if ($reset && !empty($_contents)) {
            $this
                ->reset($_contents)
                ->addActivity('read')
                ->addCustodyLogs(static::CUSTODY_LOG_KEY);
        }

        return $_contents;
    }

    /**
     * Reads the file and returns the contents
     *
     * @param bool $decoded If true (the default), the read data is decoded
     * @param int  $depth   The maximum recursion depth
     * @param int  $options Any json_decode options
     *
     * @return array|bool|string
     */
    protected function doRead($decoded = true, $depth = 512, $options = 0)
    {
        //  Always start with the current template
        $_result = $this->template;

        //  No existing file, empty array back
        if (!$this->filesystem->has($this->filename)) {
            return $_result;
        }

        //  Error reading file, false back...
        if (false === ($_json = $this->filesystem->read($this->filename))) {
            return false;
        }

        //  Not decoded gets string back
        if (!$decoded) {
            return $_json;
        }

        return static::decode($_json, true, $depth, $options);
    }

    /**
     * Writes the contents to disk
     *
     * @param bool $overwrite Overwrite an existing file
     *
     * @return bool
     */
    public function write($overwrite = true)
    {
        if (empty($this->contents)) {
            $this->reset($this->template);
        }

        if (!$overwrite && $this->filesystem->has($this->filename)) {
            throw new FileException('The file "' . $this->filename . '" already exists, and $overwrite is set to "FALSE".');
        }

        $this->addActivity('write')->addCustodyLogs(static::CUSTODY_LOG_KEY, true);

        return $this->doWrite();
    }

    /**
     * Writes the file
     *
     * @param int       $options    Any JSON encoding options
     * @param int       $depth      The maximum recursion depth
     * @param int       $retries    The number of times to retry the write.
     * @param float|int $retryDelay The number of microseconds (100000 = 1s) to wait between retries
     *
     * @return bool
     */
    protected function doWrite($options = 0, $depth = 512, $retries = self::STORAGE_OPERATION_RETRY_COUNT, $retryDelay = self::STORAGE_OPERATION_RETRY_DELAY)
    {
        $_attempts = $retries;

        //  Let's not get cray-cray
        if ($_attempts < 1) {
            $_attempts = 1;
        }

        if ($_attempts > 5) {
            $_attempts = 5;
        }

        $this->backupExistingFile();

        $_contents = $this->contents->toArray();

        if (empty($_contents) || !is_array($_contents)) {
            //  Always use the current template
            $_contents = $this->template;
        }

        while ($_attempts--) {
            try {
                if ($this->filesystem->put($this->filename, static::encode($_contents, $options, $depth))) {
                    break;
                }
                throw new FileException('Unable to write data to file "' . $this->filename . '" after ' . $retries . ' attempt(s).');
            } catch (FileException $_ex) {
                if ($_attempts) {
                    usleep($retryDelay);
                    continue;
                }

                throw $_ex;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function backupExistingFile()
    {
        //  No backups needed...
        if (!$this->makeBackups || !$this->filesystem->has($this->filename)) {
            return true;
        }

        //  Copy the file...
        if (!$this->filesystem->copy($this->filename, $this->filename . date('YmdHiS') . '.save')) {
            \Log::notice('Unable to make backup copy of "' . $this->filename . '"');

            return false;
        }

        return true;
    }

    /**
     * Gets a value from the manifest
     *
     * @param string     $key     The manifest key value to retrieve
     * @param mixed|null $default The default value to return if key was not found
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->contents->get($key, $default);
    }

    /**
     * Sets a value in the manifest
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        return $this->put($key, $value);
    }

    /**
     * Sets a value in the manifest
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function put($key, $value)
    {
        $this->contents->put($key, $value);

        return $this;
    }

    /** @inheritdoc */
    public function toArray()
    {
        return $this->contents->toArray();
    }

    /** @inheritdoc */
    public function toJson($options = 0)
    {
        return $this->contents->toJson($options);
    }

    /**
     * @return array The entire manifest
     */
    public function all()
    {
        return $this->contents->all();
    }
}