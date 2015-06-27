<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Services\Provisioners\ProvisionerOffering;

/**
 * A trait that adds offerings capabilities to provisioners
 *
 * @implements \DreamFactory\Enterprise\Common\Contracts\ProvidesOfferings
 */
trait HasOfferings
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type array My offerings
     */
    protected $offerings = null;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Boot the trait
     */
    public function bootTrait()
    {
        if (null === $this->offerings) {
            $this->offerings = [];

            /** @noinspection PhpUndefinedMethodInspection */
            $_list = config('provisioners.hosts.' . $this->getProvisionerId() . '.offerings', []);

            if (is_array($_list) && !empty($_list)) {
                foreach ($_list as $_key => $_value) {
                    if (!empty($_key)) {
                        $_offer = new ProvisionerOffering($_key, $_value);
                        $this->offerings[$_key] = $_offer->toArray();
                    }
                }
            }
        }
    }

    /**
     * Returns the list of offerings for this provider
     *
     * @return array|null Array of offerings or null if none
     */
    public function getOfferings()
    {
        return $this->offerings;
    }
}
