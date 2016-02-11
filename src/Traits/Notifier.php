<?php namespace DreamFactory\Enterprise\Common\Traits;

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
        try {
            empty($this->subjectPrefix) && $this->subjectPrefix = config('provisioning.email-subject-prefix');
            $subject = $this->subjectPrefix . ' ' . trim(str_replace($this->subjectPrefix, null, $subject));
            $data['dashboard_url'] = config('dfe.dashboard-url');
            $data['support_email_address'] = config('dfe.support-email-address');

            $_result = \Mail::send('emails.generic',
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
                    ['subject' => $subject, 'template' => 'emails.generic', 'email' => $email, 'name' => $name,])));

            return false;
        }
    }
}
