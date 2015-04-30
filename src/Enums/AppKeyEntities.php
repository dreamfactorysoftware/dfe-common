<?php namespace DreamFactory\Enterprise\Common\Enums;

use DreamFactory\Library\Fabric\Database\Enums\OwnerTypes;
use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * The types of app keys
 */
class AppKeyEntities extends FactoryEnum
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

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

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a custom app ID
     *
     * @param string $type The type of entity
     * @param string $id   The id of the entity
     *
     * @return string
     */
    public static function make( $type, $id )
    {
        static $_pattern = '[{type}:{id}]';

        if ( empty( $type ) || empty( $id ) )
        {
            throw new \InvalidArgumentException( 'Neither $type or $id may be blank.' );
        }

        return strtolower( str_replace( ['{type}', '{id}'], [$type, $id], $_pattern ) );
    }

    /**
     * @param string $entityType Given an entity type, return the associated owner type
     *
     * @return bool
     */
    public static function mapOwnerType( $entityType )
    {
        $entityType = strtoupper( trim( $entityType ) );

        return OwnerTypes::defines( $entityType, true );
    }
}
