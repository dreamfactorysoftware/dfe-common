<?php namespace DreamFactory\Enterprise\Common\Support;

use DreamFactory\Enterprise\Common\Enums\ManifestTypes;
use DreamFactory\Library\Utility\FlyJson;
use League\Flysystem\Filesystem;

/**
 * Retrieves, validates, and makes available a manifest file, if one exists.
 */
abstract class Manifest extends FlyJson
{
    //******************************************************************************
    //* Members
    //******************************************************************************

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

        $_filename =
            rtrim(str_replace('.json', null, strtolower($filename ?: ManifestTypes::nameOf($manifestType))),
                ' .' . DIRECTORY_SEPARATOR) . '.json';

        parent::__construct($filesystem, $_filename, true, $contents);
    }

}