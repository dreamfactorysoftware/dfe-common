<?php namespace DreamFactory\Enterprise\Common\Config;

use DreamFactory\Library\Console\Bags\GenericBag;
use DreamFactory\Library\Utility\FileSystem;
use DreamFactory\Library\Utility\JsonFile;

/**
 * Represents the file that contains the cluster connection information which is deployed to each web server
 *
 * {
 *      "cluster-id":       "your-cluster",
 *      "default-domain":   "your domain",
 *      "signature-method": "sha256",
 *      "storage-root":     "/data/storage",
 *      "console-api-url":  "http://console.pasture.farm.com/api/v1/ops/",
 *      "console-api-key":  "lkajhdsf;asdlfja;sdjfasdf",
 *      "client-id":        "some key",
 *      "client-secret":    "some secret"
 * }
 */
class ClusterEnvironmentBag extends GenericBag
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string The default name of the cluster environment file
     */
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