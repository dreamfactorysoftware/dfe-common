<?php namespace DreamFactory\Enterprise\Common\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * Defaults for the operations/runtime environment of DSP/DFE
 */
class EnterpriseDefaults extends FactoryEnum
{
    //*************************************************************************
    //* Defaults
    //*************************************************************************

    /**
     * @var string
     */
    const DFE_ENDPOINT = 'http://console.enterprise.dreamfactory.com/api/v1/ops';
    /**
     * @var string
     */
    const DFE_AUTH_ENDPOINT = 'http://console.enterprise.dreamfactory.com/api/v1/ops/credentials';
    /**
     * @var string
     */
    const MAINTENANCE_MARKER = '/var/www/.maintenance';
    /**
     * @var string
     */
    const MAINTENANCE_URI = '/static/dreamfactory/maintenance.php';
    /**
     * @var string
     */
    const UNAVAILABLE_URI = '/static/dreamfactory/unavailable.php';
    /**
     * @var int
     */
    const EXPIRATION_THRESHOLD = 30;
    /**
     * @var string Public storage cookie key
     */
    const PUBLIC_STORAGE_COOKIE = 'dfe.public-id';
    /**
     * @var string Private storage cookie key
     */
    const PRIVATE_STORAGE_COOKIE = 'dfe.private-id';
    /** @type string The default hash algorithm to use for signing requests */
    const DEFAULT_SIGNATURE_METHOD = 'sha256';
    /**
     * @type int The default number of days to keep a snapshots available
     */
    const SNAPSHOT_DAYS_TO_KEEP = 30;
    /**
     * @type bool If true, expired snapshots are moved to a "trash" area for removal via other means (or long-term
     *       storage). Otherwise, expired snapshots are physically deleted. The latter being the default.
     */
    const SNAPSHOT_SOFT_DELETE = false;
    /**
     * @type string The manifest file name
     */
    const CLUSTER_MANIFEST_FILE_NAME = '.dfe.cluster.json';
    /**
     * @type string The default path of the blueprint repository
     */
    const DEFAULT_BLUEPRINT_REPO_PATH = '/data/blueprints';
    /**
     * @type string The default "trash" path for soft-deleted snapshots
     */
    const DEFAULT_TRASH_PATH = '/data/trash';
    /** @type string The name of the private path */
    const PRIVATE_PATH_NAME = '.private';
    /** @type string The name of the snapshots path */
    const SNAPSHOT_PATH_NAME = 'snapshots';
    /** @type string The default prefix for outgoing email */
    const EMAIL_SUBJECT_PREFIX = '[DFE]';
    /** @type string The default hash algorithm for hashing */
    const DEFAULT_HASH_ALGORITHM = 'sha256';
    /** @type string The hash algorithm to use for signing requests */
    const SIGNATURE_METHOD = self::DEFAULT_HASH_ALGORITHM;
    /** @type string The default export/mount point/directory where instance data lives */
    const STORAGE_ROOT = '/data/storage';
    /** @type string The value to put in the image name field for hosted instances */
    const DFE_CLUSTER_BASE_IMAGE = 'dfe.standard';
    /**
     * @type string
     */
    const DEFAULT_HANDLER_NAMESPACE = 'DreamFactory\\Enterprise\\Services\\Handlers\\Commands\\';
    /** @type string The name of the cluster manifest file */
    const CLUSTER_MANIFEST_FILE = '.dfe.cluster.json';
    /**
     * @type string The type of compression to use when making exports
     */
    const DEFAULT_DATA_COMPRESSOR = 'zip';
    /**
     * @type string The default required storage paths
     */
    const DEFAULT_REQUIRED_STORAGE_PATHS = 'applications|.private';
    /**
     * @type string The default required private paths
     */
    const DEFAULT_REQUIRED_PRIVATE_PATHS = '.cache|.limits_cache|config|scripts|scripts.user|logs|packages';
    /**
     * @type string The default required private paths
     */
    const DEFAULT_REQUIRED_OWNER_PRIVATE_PATHS = 'snapshots';
    /**
     * @type int The default number of items to display on a listing of data
     */
    const DEFAULT_ITEMS_PER_PAGE = 25;
    /**
     * @type string The default protocol for building urls (http or https)
     */
    const DEFAULT_DOMAIN_PROTOCOL = 'http';
    /** @type string X Header for the console to use to authenticate with a 2.0 instance */
    const CONSOLE_X_HEADER = 'X-DreamFactory-Console-Key';
    /** @type string X Header for an app to talk with a 2.0 instance */
    const INSTANCE_API_HEADER = 'X-DreamFactory-API-Key';
    /**
     * @type int The default number of days to keep password resets
     */
    const DEFAULT_RESETS_DAYS_TO_KEEP = 1;
    /**
     * @type int The default number of days to keep system metrics
     */
    const DEFAULT_METRICS_DAYS_TO_KEEP = 180;
    /**
     * @type int The default number of days to keep system metrics
     */
    const DEFAULT_METRICS_DETAIL_DAYS_TO_KEEP = 7;
    /**
     * @type int The default number of days an instance may remain non-active
     */
    const DEFAULT_ADS_ACTIVATE_BY_DAYS = 7;
    /**
     * @type int The allowed number of activation extensions
     */
    const DEFAULT_ADS_ALLOWED_EXTENDS = 0;
    /**
     * @type int The allowed number days an instance may remain unused/idle
     */
    const DEFAULT_ADS_ALLOWED_IDLE_DAYS = 30;
    /**
     * @type string The default path to store provisioning packages (under STORAGE_ROOT
     */
    const DEFAULT_PACKAGE_STORAGE_PATH = 'packages';
}
