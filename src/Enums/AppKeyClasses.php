<?php namespace DreamFactory\Enterprise\Common\Enums;

use DreamFactory\Library\Fabric\Database\Enums\OwnerTypes;
use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * The classes of app keys
 */
class AppKeyClasses extends FactoryEnum
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @var string
     */
    const TYPE_ENTITY = 'entity';
    /**
     * @var string
     */
    const CONSOLE = '[entity:console]';
    /**
     * @var string
     */
    const DASHBOARD = '[entity:dashboard]';
    /**
     * @var string
     */
    const APPLICATION = '[entity:application]';
    /**
     * @var string
     */
    const SERVICE = '[entity:service]';
    /**
     * @var string
     */
    const USER = '[entity:user]';
    /**
     * @var string
     */
    const INSTANCE = '[entity:instance]';
    /**
     * @var string
     */
    const SERVER = '[entity:server]';
    /**
     * @var string
     */
    const CLUSTER = '[entity:cluster]';
    /**
     * @var string
     */
    const OTHER = '[entity:other]';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a custom app ID
     *
     * @param string $type   The specific entity type
     * @param string $entity The entity classification/term. Defaults to generic "entity"
     *
     * @return string
     */
    public static function make( $type, $entity = self::TYPE_ENTITY )
    {
        static $_pattern = '[{entity}:{type}]';

        if ( empty( $entity ) || empty( $type ) )
        {
            throw new \InvalidArgumentException( 'Neither $entity or $type may be blank.' );
        }

        return strtolower( str_replace( ['{entity}', '{id}'], [$entity, $type], $_pattern ) );
    }

    /**
     * Given an owner type, return a key class
     *
     * @param int    $ownerType The type of owner
     * @param string $entity    The entity classification/term. Defaults to generic "entity"
     *
     * @return string
     */
    public static function fromOwnerType( $ownerType, $entity = self::TYPE_ENTITY )
    {
        $_name = strtolower( OwnerTypes::nameOf( $ownerType ) );

        return static::make( static::defines( $_name, true ), $entity );
    }

    /**
     * @param string $entityType Given an entity type, return the associated owner type
     *
     * @return bool
     */
    public static function mapOwnerType( $entityType )
    {
        return OwnerTypes::defines( strtoupper( trim( $entityType ) ), true );
    }
}
