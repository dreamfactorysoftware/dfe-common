<?php namespace DreamFactory\Enterprise\Common\Provisioners;

use DreamFactory\Enterprise\Common\Contracts\PrivatePathAware;
use DreamFactory\Enterprise\Common\Contracts\VirtualProvisioner;
use DreamFactory\Enterprise\Common\Traits\Archivist;
use DreamFactory\Enterprise\Common\Traits\HasPrivatePaths;
use DreamFactory\Enterprise\Database\Traits\InstanceValidation;

abstract class BaseStorageProvisioner extends BaseProvisioningService implements PrivatePathAware, VirtualProvisioner
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use InstanceValidation, Archivist, HasPrivatePaths;
}
