<?php namespace DreamFactory\Enterprise\Common\Contracts;

/**
 * Something that is portable
 */
interface Portability
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Imports something from somewhere
     *
     * @param mixed|\DreamFactory\Enterprise\Services\Provisioners\ProvisioningRequest $request A generic provisioning request
     * @param mixed|\League\Flysystem\Filesystem                                       $from    The source of the import
     * @param mixed|array                                                              $options vendor-specific implementation options
     *
     * @return mixed
     */
    public function import($request, $from, $options = []);

    /**
     * Exports something to somewhere
     *
     * @param mixed|\DreamFactory\Enterprise\Services\Provisioners\ProvisioningRequest $request A generic provisioning request
     * @param mixed|\League\Flysystem\Filesystem                                       $to      The destination of the export
     * @param mixed|array                                                              $options vendor-specific implementation options
     *
     * @return mixed
     */
    public function export($request, $to, $options = []);
}
