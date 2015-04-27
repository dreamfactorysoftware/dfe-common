<?php namespace DreamFactory\Enterprise\Common\Enums;

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
    const PATTERN = '[{type}:{id}]';
    /**
     * @var string
     */
    const USER = '[entity:user]';
    /**
     * @var string
     */
    const SERVICE = '[entity:service]';
    /**
     * @var string
     */
    const SERVER = '[entity:server]';
    /**
     * @var string
     */
    const CLUSTER = '[entity:cluster]';

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
        if ( empty( $type ) || empty( $id ) )
        {
            throw new \InvalidArgumentException( 'Neither $type or $id may be blank.' );
        }

        return str_replace( ['{type}', '{id}'], [$type, $id], static::PATTERN );
    }
}
