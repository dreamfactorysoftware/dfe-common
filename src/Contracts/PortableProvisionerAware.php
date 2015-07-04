<?php
namespace DreamFactory\Enterprise\Common\Contracts;

use DreamFactory\Enterprise\Services\Jobs\ExportJob;
use DreamFactory\Enterprise\Services\Jobs\ImportJob;

/**
 * Something that is aware of provisioners
 */
interface PortableProvisionerAware
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * @param string|null $name The provisioner ID
     *
     * @return PortableData[]|[]
     */
    public function getPortableServices($name = null);

    /**
     * Export portability data
     *
     * @param ExportJob $job
     *
     * @return mixed
     */
    public function export(ExportJob $job);

    /**
     * Import portability data
     *
     * @param ImportJob $job
     *
     * @return mixed
     */
    public function import(ImportJob $job);
}