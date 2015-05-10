<?php
namespace DreamFactory\Enterprise\Common\Enums;

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
     * @type string
     */
    const BOOTSTRAP_FILE = 'bootstrap.config.php';
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
    const PUBLIC_STORAGE_COOKIE = 'dfe-console.public-id';
    /**
     * @var string Private storage cookie key
     */
    const PRIVATE_STORAGE_COOKIE = 'dfe-console.private-id';
    /**
     * @type string
     */
    const DEFAULT_ENVIRONMENT_CLASS = '\\DreamFactory\\Library\\Utility\\Environment';
    /**
     * @type string
     */
    const DEFAULT_RESOLVER_CLASS = '\\DreamFactory\\Library\\Enterprise\\Storage\\Resolver';
    /** @type string The default hash algorithm to use for creating structure */
    const DEFAULT_DATA_STORAGE_HASH = 'sha256';
    /** @type string The default hash algorithm to use for signing requests */
    const DEFAULT_SIGNATURE_METHOD = 'sha256';
}
