<?php namespace DreamFactory\Enterprise\Common\Provisioners;

use DreamFactory\Enterprise\Common\Contracts\VirtualProvisioner;
use DreamFactory\Enterprise\Common\Enums\EnterpriseDefaults;
use DreamFactory\Enterprise\Common\Enums\EnterprisePaths;
use DreamFactory\Enterprise\Common\Traits\EntityLookup;
use DreamFactory\Enterprise\Common\Traits\HasTimer;
use DreamFactory\Enterprise\Common\Traits\LockingService;
use DreamFactory\Enterprise\Common\Traits\Notifier;
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
    //* Constants
    //******************************************************************************

    /**
     * @type string Our resource URI
     */
    const RESOURCE_URI = false;

    //******************************************************************************
    //* Traits
    //******************************************************************************

    use InstanceValidation, LockingService, Notifier, HasTimer, EntityLookup;

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

    /**
     * @param BaseRequest|mixed $request
     *
     * @return BaseResponse|mixed
     */
    public function provision($request)
    {
        $this->startTimer();
        $_response = $this->doProvision($request);
        $_response->setElapsedTime($_elapsed = $this->stopTimer());
        $this->audit(['elapsed' => $_elapsed, 'result' => $_response->getResult()]);

        //  Save results...
        /** @type Instance $_instance */
        $_instance = $request->getInstance();
        $_instance->addOperation('provision', $_response->getResult());

        //  Send notification
        $_guest = $_instance->guest;
        $_host = ($_guest && $_guest->public_host_text)
            ? $_guest->public_host_text
            : $_instance->instance_id_text . '.' . trim(config('provisioning.default-dns-zone'),
                '.') . '.' . trim(config('provisioning.default-dns-domain'), '.');

        $_data = [
            'firstName'     => $_instance->user->first_name_text,
            'headTitle'     => $_response->isSuccess() ? 'Launch Complete' : 'Launch Failure',
            'contentHeader' => $_response->isSuccess() ? 'Your instance has been launched'
                : 'Your instance was not launched',
            'emailBody'     => $_response->isSuccess()
                ? '<p>Your instance <strong>' .
                $_instance->instance_name_text .
                '</strong> ' .
                'has been created. You can reach it by going to <a href="//' .
                $_host .
                '">' .
                $_host .
                '</a> from any browser.</p>'
                : '<p>Your instance <strong>' .
                $_instance->instance_name_text .
                '</strong> ' .
                'was not created. Our engineers will examine the issue and notify you when it has been resolved. Hang tight, we\'ve got it.</p>',
        ];

        $_subject = $_response->isSuccess() ? 'Instance launch successful' : 'Instance launch failure';

        $this->notifyInstanceOwner($_instance, $_subject, $_data);

        return $_response;
    }

    /**
     * @param BaseRequest|mixed $request
     *
     * @return BaseResponse|mixed
     */
    public function deprovision($request)
    {
        $this->startTimer();

        //  Save results...
        /** @type Instance $_instance */
        $_instance = $request->getInstance();
        $_instance->addOperation('deprovision');

        $_response = $this->doDeprovision($request);
        $_response->setElapsedTime($_elapsed = $this->stopTimer());
        $this->audit(['elapsed' => $_elapsed, 'result' => $_result = $_response->getResult()]);

        //  Send notification
        $_data = [
            'firstName'     => $_instance->user->first_name_text,
            'headTitle'     => $_response->isSuccess() ? 'Retirement Complete' : 'Retirement Failure',
            'contentHeader' => $_response->isSuccess() ? 'Your instance has been retired'
                : 'Your instance is not quite retired',
            'emailBody'     => $_response->isSuccess()
                ? '<p>Your instance <strong>' .
                $_instance->instance_name_text .
                '</strong> has been retired.  A snapshot may be available in the dashboard, under <strong>Snapshots</strong>.</p>'
                : '<p>Your instance <strong>' .
                $_instance->instance_name_text .
                '</strong> retirement was not successful. Our engineers will examine the issue and, if necessary, notify you if/when the issue has been resolved. Mostly likely you will not have to do a thing. But we will check it out just to be safe.</p>',
        ];

        $_subject = $_response->isSuccess() ? 'Instance retirement successful' : 'Instance retirement failure';

        $this->notifyInstanceOwner($_instance, $_subject, $_data);

        return $_response;
    }

}