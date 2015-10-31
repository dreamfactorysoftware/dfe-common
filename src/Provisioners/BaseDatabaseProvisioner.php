<?php namespace DreamFactory\Enterprise\Common\Provisioners;

use DreamFactory\Enterprise\Common\Contracts\VirtualProvisioner;
use DreamFactory\Enterprise\Common\Traits\Archivist;
use DreamFactory\Enterprise\Common\Traits\HasPrivatePaths;
use DreamFactory\Enterprise\Database\Traits\InstanceValidation;

abstract class BaseDatabaseProvisioner extends BaseProvisioningService implements VirtualProvisioner
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use InstanceValidation, Archivist, HasPrivatePaths;
}
