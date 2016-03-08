<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Common\Enums\EnterpriseDefaults;
use DreamFactory\Enterprise\Database\Models\Instance;
use DreamFactory\Library\Utility\Json;
use Exception;
use Illuminate\Mail\Message;
use InvalidArgumentException;
use Log;
use Mail;

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
            $subject = trim(str_replace($this->subjectPrefix, null, $subject));
            $_bccSubject = $this->subjectPrefix . ' ' . trim(str_replace($this->subjectPrefix, null, $subject));

            $data = array_merge($this->getNotificationDefaultData(), $data);

            Mail::send($_view,
                $data,
                function($message/** @var Message $message */) use ($email, $name, $subject) {
                    $message->from(config('mail.from.address'), config('mail.from.name'));
                    $message->subject($subject);
                    $message->to($email, $name);
                });

            if (config('license.bcc-notifications', false)) {
                Mail::send($_view,
                    $data,
                    function($message/** @var Message $message */) use ($email, $name, $_bccSubject) {
                        $message->from(config('mail.from.address'), config('mail.from.name'));
                        $message->subject($_bccSubject);
                        $message->to(config('license.notification-address'), 'DreamFactory');
                    });
            }

            logger('notification sent to "' . $email . '"');

            return true;
        } catch (Exception $_ex) {
            Log::error('Error sending notification: ' . $_ex->getMessage());

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
        //  Bogus operation?
        if (empty($_config = config('notifications.templates.' . trim(strtolower($operation))))) {
            throw new InvalidArgumentException('The operation "' . $operation . '" is invalid.');
        }

        /** @type Instance $_instance */
        if (empty($_instance = array_get($data, 'instance'))) {
            $data['instance'] = $_instance = false;
            $data['instanceUrl'] = false;
            $data['instanceName'] = array_get($data, 'instanceName');
        } else {
            $data['instanceUrl'] = $_instance->getProvisionedEndpoint();
            $data['instanceName'] = $_instance->instance_id_text;
        }

        if ($_instance && $_instance->user && null === ($_firstName = array_get($data, 'firstName'))) {
            $data['firstName'] = $_firstName = $_instance->user->first_name_text;
        }

        $data['headTitle'] = array_get($data, 'headTitle', array_get($_config, 'subject', 'System Notification'));
        $data['contentHeader'] = array_get($data, 'contentHeader', array_get($_config, 'title', 'System Notification'));
        $data['email-view'] = $view ?: array_get($_config, 'view', 'emails.generic');
        $data['emailBody'] = array_get($data, 'emailBody');
        $data['daysToKeep'] = array_get($data, 'daysToKeep', config('snapshot.days-to-keep', EnterpriseDefaults::SNAPSHOT_DAYS_TO_KEEP));

        return $this->notify($email, $name, $data['headTitle'], $data);
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
