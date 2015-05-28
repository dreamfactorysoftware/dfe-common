<?php namespace DreamFactory\Enterprise\Common\Config;

use DreamFactory\Library\Console\Bags\GenericBag;
use DreamFactory\Library\Utility\FileSystem;
use DreamFactory\Library\Utility\JsonFile;

/**
 * Represents the file that contains the cluster connection information that is deployed to each web server
 *
 * {
 *      "cluster-id":       "cluster-east-2",
 *      "default-domain":   ".enterprise.dreamfactory.com",
 *      "signature-method": "sha256",
 *      "storage-root":     "/data/storage",
 *      "console-api-url":  "http://console.enterprise.dreamfactory.com/api/v1/ops/",
 *      "console-api-key":  "%]3,]~&t,EOxL30[wKw3auju:[+L>eYEVWEP,@3n79Qy",
 *      "client-id":        "28b23fedb0b186fc00e9dceba473a3326f36fbc79b390c615a199603fdb1b13f",
 *      "client-secret":    "5a1a84735446812372ae7e380a413348a7b94e42b444424abed0b5197678d625"
 * }
 */
class ClusterBag extends GenericBag
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    const ENVIRONMENT_FILE_NAME = '.env.cluster.json';

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type bool If true, backups of existing files will be created before being overwritten
     */
    protected static $_makeBackups = true;
    /**
     * @type string The name of the cluster environment file
     */
    protected $_filename = self::ENVIRONMENT_FILE_NAME;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string                    $id A name for this bag
     * @param string                    $filename
     * @param array|object|\Traversable $defaultContents
     * @param bool                      $makeBackups
     */
    public function __construct( $id, $filename, $defaultContents = [], $makeBackups = true )
    {
        parent::__construct( $id, $defaultContents );

        if ( !FileSystem::ensurePath( $filename ) )
        {
            throw new \InvalidArgumentException( 'The path "' . dirname( $filename ) . '" is invalid.' );
        }

        $this->_filename = $filename;
    }

    /**
     * @param string $filename
     * @param array  $extraData  Any extra data to add to the file
     * @param int    $options    json_decode options
     * @param int    $depth      The maximum recursion
     * @param int    $retries    The number of times to retry writing the file
     * @param int    $retryDelay The delay between attempts
     */
    public function save( $filename = null, $extraData = [], $options = 0, $depth = 512, $retries = JsonFile::STORAGE_OPERATION_RETRY_COUNT, $retryDelay = JsonFile::STORAGE_OPERATION_RETRY_DELAY )
    {
        JsonFile::encodeFile(
            $filename ?: $this->_filename,
            array_merge( $extraData, $this->all() ),
            $options,
            $depth,
            $retries,
            $retryDelay
        );
    }

    /**
     * @param string $filename
     * @param bool   $asArray Return data in an array (default), or false to get data back in an object
     * @param int    $depth   The maximum recursion
     * @param int    $options json_decode options
     *
     * @return array|\stdClass
     */
    public function load( $filename = null, $asArray = true, $depth = 512, $options = 0 )
    {
        return JsonFile::decodeFile(
            $filename ?: $this->_filename,
            $depth = 512,
            $options
        );
    }
}