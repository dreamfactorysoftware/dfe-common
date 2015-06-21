<?php namespace DreamFactory\Enterprise\Common\Support;

use DreamFactory\Enterprise\Common\Enums\ManifestTypes;
use DreamFactory\Enterprise\Common\Traits\Custodian;
use DreamFactory\Library\Utility\Exceptions\FileException;
use DreamFactory\Library\Utility\Json;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;

/**
 * Retrieves, validates, and makes available the a manifest file, if one exists.
 */
abstract class Manifest implements Arrayable, Jsonable
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use Custodian;

    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string The key used by the Custodian
     */
    const CUSTODY_LOG_KEY = '_usage';

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type Collection The contents of the manifest
     */
    protected $contents;
    /**
     * @type Filesystem The filesystem where the manifest lives
     */
    protected $filesystem;
    /**
     * @type string The name of the manifest
     */
    protected $manifest;
    /**
     * @type array The template for the manifest
     */
    protected $template = [];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param \League\Flysystem\Filesystem $filesystem   The filesystem where the manifest lives
     * @param string                       $manifestType The type of manifest @see ManifestTypes
     * @param array                        $contents     Optional contents to fill
     * @param string                       $filename     If you do not want the default manifest name for the type, override it with this
     */
    public function __construct(Filesystem $filesystem, $manifestType, $contents = [], $filename = null)
    {
        if (!ManifestTypes::contains($manifestType)) {
            throw new \InvalidArgumentException('The $manifestType "' . $manifestType . '" is invalid.');
        }

        $this->filesystem = $filesystem;
        $this->manifest =
            ($filename ? rtrim(str_replace('.json', null, $filename), ' .' . DIRECTORY_SEPARATOR) : null) . '.json';

        $this->initialize($contents);
    }

    /**
     * @param array $contents The contents with which to initialize. Merged with existing manifest data
     *
     * @return $this
     */
    protected function initialize($contents = [])
    {
        if (!is_array($contents) || empty($contents)) {
            $contents = [];
        }

        $this->contents = new Collection(array_merge($this->read(false), $contents));

        return $this;
    }

    /**
     * Fill the manifest with fresh contents with template checking
     *
     * @param array $contents
     *
     * @return $this
     */
    public function fill(array $contents = [])
    {
        foreach ($contents as $_key => $_value) {
            if (empty($this->template) || array_key_exists($_key, $this->template)) {
                $this->contents->put($_key, $_value);
            }
        }

        return $this;
    }

    /**
     * Reads and loads the manifest into memory
     *
     * @param bool $reset If true (the default) the contents are reset and loaded from manifest file. If false, the
     *                    manifest is returned but the current contents are left undisturbed.
     *
     * @return $this|array
     */
    public function read($reset = true)
    {
        //  The default manifest, if none exists
        $_manifest = $this->template;

        if ($this->filesystem->has($this->manifest)) {
            try {
                if (false !== ($_json = $this->filesystem->read($this->manifest))) {
                    $_manifest = Json::decode($_json, true);
                }
            } catch (FileNotFoundException $_ex) {
                //  Not there, ignored.
            }
        }

        $this->addActivity('read');

        //  If not resetting, return data read
        if (!$reset) {
            return $_manifest;
        }

        $this->contents = new Collection($_manifest);

        return $this;
    }

    /**
     * Writes the manifest to disk
     *
     * @param bool $overwrite Overwrite any existing manifest
     *
     * @return bool
     */
    public function write($overwrite = true)
    {
        if (empty($this->contents)) {
            return false;
        }

        if (!$overwrite && $this->filesystem->has($this->manifest)) {
            throw new FileException('A manifest already exists and $overwrite is set to "FALSE".');
        }

        $this->addActivity('write');

        return $this->filesystem->put($this->manifest, $this->contents->toJson());
    }

    /**
     * Gets a value from the manifest
     *
     * @param string $key     The manifest key value to retrieve
     * @param mixed  $default The default value to return if key was not found
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
        return $this->contents->put($key, $value);
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