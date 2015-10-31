<?php namespace DreamFactory\Enterprise\Common\Provisioners;

use DreamFactory\Enterprise\Common\Contracts\VirtualProvisioner;
use DreamFactory\Enterprise\Common\Exceptions\NotImplementedException;
use DreamFactory\Enterprise\Common\Services\BaseService;

abstract class BaseProvisioningService extends BaseService implements VirtualProvisioner
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string Your provisioner id
     */
    const PROVISIONER_ID = false;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param BaseRequest $request
     *
     * @return BaseResponse
     */
    abstract protected function doProvision($request);

    /**
     * @param BaseRequest $request
     *
     * @return BaseResponse
     */
    abstract protected function doDeprovision($request);

    /**
     * @param array $data
     * @param int   $level
     * @param bool  $deprovisioning
     *
     * @return bool
     */
    protected function audit($data = [], $level = 6 /* info */, $deprovisioning = false)
    {
        if (function_exists('audit')) {
            //  Put instance ID into the correct place
            isset($data['instance']) && $data['dfe'] = ['instance_id' => $data['instance']->instance_id_text];

            return audit($data, $level, ($deprovisioning ? 'de' : null) . 'provision');
        }

        return false;
    }

    /** @inheritdoc */
    public function provision($request)
    {
        return $this->doProvision($request);
    }

    /** @inheritdoc */
    public function deprovision($request)
    {
        return $this->doDeprovision($request);
    }

    /** @inheritdoc */
    public function getProvisionerId()
    {
        if (!static::PROVISIONER_ID) {
            throw new NotImplementedException('No provisioner id has been set.');
        }

        return static::PROVISIONER_ID;
    }

}
