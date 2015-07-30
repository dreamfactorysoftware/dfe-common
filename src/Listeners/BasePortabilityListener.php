<?php namespace DreamFactory\Enterprise\Common\Listeners;

use DreamFactory\Enterprise\Common\Provisioners\PortableServiceRequest;

/**
 * A base class for listeners. Includes entity lookup and logging
 */
class BasePortabilityListener extends BaseListener
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Handle a provisioning request
     *
     * @param \DreamFactory\Enterprise\Services\Jobs\ExportJob $job
     *
     * @return mixed
     *
     */
    public function handle(ExportJob $job)
    {
        $_start = microtime(true);
        $_instanceId = $job->getInstanceId();

        $this->debug('>>> export "' . $_instanceId . '" request received');

        try {
            $job->setResult($_result = Snapshot::createFromExports($job->getInstance(), Provision::export($job)));
            $this->debug('<<< export "' . $_instanceId . '" request SUCCESS');
        } catch (\Exception $_ex) {
            $this->error('<<< export "' . $_instanceId . '" request FAILURE: ' . $_ex->getMessage());
            $_result = false;
        }

        $_elapsed = microtime(true) - $_start;
        $this->debug('export complete in ' . number_format($_elapsed, 4) . 's');

        return $_result;
    }
}
