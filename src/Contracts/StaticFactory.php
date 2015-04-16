<?php
namespace DreamFactory\Enterprise\Common\Contracts;

/**
 * Describes a service that can create things statically
 */
interface StaticFactory
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a new thing
     *
     * @param string $abstract The abstract name of the thing
     * @param array  $data     Array of data used to make the thing
     *
     * @return array
     */
    public static function make( $abstract, $data = [] );

}