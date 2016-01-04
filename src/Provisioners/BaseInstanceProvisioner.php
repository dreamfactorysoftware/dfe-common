<?php namespace DreamFactory\Enterprise\Common\Provisioners;

use DreamFactory\Enterprise\Common\Contracts\VirtualProvisioner;
use DreamFactory\Enterprise\Common\Enums\EnterpriseDefaults;
use DreamFactory\Enterprise\Common\Enums\EnterprisePaths;
use DreamFactory\Enterprise\Common\Traits\EntityLookup;
use DreamFactory\Enterprise\Common\Traits\LockingService;
use DreamFactory\Enterprise\Database\Enums\ProvisionStates;
use DreamFactory\Enterprise\Database\Models\Instance;
use DreamFactory\Enterprise\Database\Traits\InstanceValidation;

/**
 * A base class for all provisioners
 *
 * This class provides a foundation upon which to build other PaaS provisioners for the DFE ecosystem. Merely extend
 * the class and add the
 * _doProvision and _doDeprovision methods.
 *
 * @todo Move all english text to templates
 */
abstract class BaseInstanceProvisioner extends BaseProvisioningService implements VirtualProvisioner
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use InstanceValidation, LockingService, EntityLookup;

    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string Our resource URI
     */
    const RESOURCE_URI = false;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type array The default cluster environment file template.
     */
    protected static $envTemplate = [
        'cluster-id'       => null,
        'default-domain'   => null,
        'signature-method' => EnterpriseDefaults::SIGNATURE_METHOD,
        'storage-root'     => EnterprisePaths::DEFAULT_HOSTED_BASE_PATH,
        'api-url'          => null,
        'api-key'          => null,
        'client-id'        => null,
        'client-secret'    => null,
    ];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function boot()
    {
        parent::boot();

        $this->setLumberjackPrefix($this->getProvisionerId());

        if (empty($this->subjectPrefix)) {
            $this->subjectPrefix = config('dfe.email-subject-prefix', EnterpriseDefaults::EMAIL_SUBJECT_PREFIX);
        }
    }

    /**
     * Get the current status of an instance
     *
     * @param Instance $instance
     *
     * @return array
     */
    public function status($instance)
    {
        /** @var Instance $_instance */
        if (null === ($_instance = Instance::find($instance->id))) {
            return ['success' => false, 'error' => ['code' => 404, 'message' => 'Instance not found.']];
        }

        return [
            'success'     => true,
            'status'      => $_instance->state_nbr,
            'status_text' => ProvisionStates::prettyNameOf($_instance->state_nbr),
        ];
    }
}
