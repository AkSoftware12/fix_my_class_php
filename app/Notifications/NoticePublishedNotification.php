<?php

namespace App\Notifications;

use App\Models\Notice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NoticePublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Notice $notice)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (setting('notify_email_enabled', '1') === '1' && setting('notify_notices', '1') === '1') {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Notice: '.$this->notice->title)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('A new notice has been published.')
            ->line($this->notice->title)
            ->line('Log in to read the full notice.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => ucfirst($this->notice->type).' notice',
            'body' => $this->notice->title,
            'url' => route('admin.notices.show', $this->notice),
            'notice_id' => $this->notice->id,
        ];
    }
}
