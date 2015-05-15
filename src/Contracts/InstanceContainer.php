<?php
namespace DreamFactory\Enterprise\Common\Contracts;

use DreamFactory\Enterprise\Database\Models\Instance;

/**
 * Defines an object that acts as a container for an instance
 */
interface InstanceContainer
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return Instance
     */
    public function getInstance();

}