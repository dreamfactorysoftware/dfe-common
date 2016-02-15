<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Console\Enums\ConsoleOperations;
use DreamFactory\Enterprise\Database\Models\Instance;
use DreamFactory\Enterprise\Database\Models\ServiceUser;
use DreamFactory\Library\Utility\Json;
use Illuminate\Mail\Message;

/**
 * A trait that aids with notifying
 */
trait Notifier
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string|null The prefix for outbound subject lines
     */
    protected $subjectPrefix;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param Instance $instance
     * @param string   $subject
     * @param array    $data
     *
     * @return int The number of recipients mailed
     */
    protected function notifyInstanceOwner($instance, $subject, array $data)
    {
        $_name = trim($instance->user->first_name_text . ' ' . $instance->user->last_name_text) ?: $instance->user->email_addr_text;

        return $this->notify($instance->user->email_addr_text, $_name, $subject, $data);
    }

    /**
     * @param string $email
     * @param string $name
     * @param string $subject
     * @param array  $data
     *
     * @return int The number of recipients mailed
     */
    protected function notify($email, $name, $subject, array $data)
    {
        $_view = array_get($data, 'email-view', 'emails.generic');

        try {
            $this->subjectPrefix = $this->subjectPrefix ?: config('provisioning.email-subject-prefix');
            $subject = $this->subjectPrefix . ' ' . trim(str_replace($this->subjectPrefix, null, $subject));

            $data = array_merge($this->getNotificationDefaultData(), $data);

            $_result = \Mail::send($_view,
                $data,
                function($message/** @var Message $message */) use ($email, $name, $subject) {
                    $message->from(config('mail.from.address'), config('mail.from.name'));
                    $message->subject($subject);
                    $message->to($email, $name);
                    $message->bcc(config('license.notification-address'), 'DreamFactory Operations');
                });

            \Log::debug('notification sent to "' . $email . '"');

            return $_result;
        } catch (\Exception $_ex) {
            \Log::error('Error sending notification: ' . $_ex->getMessage());

            $_mailPath = storage_path('logs/unsent-mail');

            if (!is_dir($_mailPath)) {
                mkdir($_mailPath, 0777, true);
            }

            @file_put_contents(date('YmdHis') . '-' . $email . '.json',
                Json::encode(array_merge($data,
                    ['subject' => $subject, 'email-view' => $_view, 'email' => $email, 'name' => $name,])));

            return false;
        }
    }

    /**
     * @param string $operation The job type
     * @param string $email     The email of the recipient
     * @param string $name      The recipient's name
     * @param array  $data      Any view data
     * @param string $view      The email view to use
     *
     * @see \DreamFactory\Enterprise\Console\Enums\ConsoleOperations
     * @return int
     */
    protected function notifyJobOwner($operation, $email, $name, array $data = [], $view = null)
    {
        /** @type Instance $_instance */
        if (null !== ($_instance = array_get($data, 'instance'))) {
            if (false === $_instance) {
                $data['instanceUrl'] = false;
                $data['instanceName'] = array_get($data, 'instanceName');
            } else {
                $data['instanceUrl'] = $_instance->getProvisionedEndpoint();
                $data['instanceName'] = $_instance->instance_name_text;
            }
        }

        if ($_instance && null === ($_firstName = array_get($data, 'firstName'))) {
            $_firstName = $_instance->user->first_name_text;
            $data['firstName'] = $_firstName;
        }

        $_headTitle = array_get($data, 'headTitle');
        $_contentHeader = array_get($data, 'contentHeader');

        switch (trim(strtolower($operation))) {
            case ConsoleOperations::METRICS:
                !$_headTitle && $_headTitle = 'Metrics Complete';
                !$_contentHeader && $_contentHeader = 'Metrics have been generated successfully';
                $view = $view ?: 'emails.generic';
                break;

            case ConsoleOperations::PROVISION:
                !$_headTitle && $_headTitle = 'Provisioning Complete';
                !$_contentHeader && $_contentHeader = 'Your new instance is ready';
                $view = $view ?: 'emails.provision';
                break;

            case ConsoleOperations::DEPROVISION:
                !$_headTitle && $_headTitle = 'Deprovisioning Complete';
                !$_contentHeader && $_contentHeader = 'Your instance has been retired';
                $view = $view ?: 'emails.deprovision';
                break;

            case ConsoleOperations::IMPORT:
                !$_headTitle && $_headTitle = 'Import Complete';
                !$_contentHeader && $_contentHeader = 'Your imported instance is ready';
                $view = $view ?: 'emails.import';
                break;

            case ConsoleOperations::EXPORT:
                !$_headTitle && $_headTitle = 'Export Complete';
                !$_contentHeader && $_contentHeader = 'Your export is complete';
                $view = $view ?: 'emails.export';
                break;

            default:
                throw new \InvalidArgumentException('The operation "' . $operation . '" is invalid.');
        }

        ($_headTitle && !isset($data['headTitle'])) && $data['headTitle'] = $_headTitle;
        ($_contentHeader && !isset($data['contentHeader'])) && $data['contentHeader'] = $_contentHeader;
        $data['email-view'] = $view;

        return $this->notify($email, $name, $_headTitle, $data);
    }

    /**
     * @return array The view data that is always available
     */
    protected function getNotificationDefaultData()
    {
        return [
            'dashboard_url'         => config('dfe.dashboard-url'),
            'support_email_address' => config('dfe.support-email-address'),
        ];
    }
}
