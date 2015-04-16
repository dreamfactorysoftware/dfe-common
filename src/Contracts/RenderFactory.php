<?php
namespace DreamFactory\Enterprise\Common\Contracts;

/**
 * Describes a service that can create and render things
 */
interface RenderFactory extends Factory
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Render a thing
     *
     * @param string   $abstract The abstract name of the thing to create
     * @param array    $data     Any data needed to render the thing
     * @param \Closure $callback
     *
     * @return mixed
     */
    public function render( $abstract, $data = [], \Closure $callback = null );
}