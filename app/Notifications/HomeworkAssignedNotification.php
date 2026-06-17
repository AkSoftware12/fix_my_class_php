<?php

namespace App\Notifications;

use App\Models\Homework;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HomeworkAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Homework $homework)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (setting('notify_email_enabled', '1') === '1' && setting('notify_homework', '1') === '1') {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Homework: '.$this->homework->title)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('New homework has been assigned to you.')
            ->line('Title: '.$this->homework->title)
            ->line($this->homework->due_date ? 'Due date: '.$this->homework->due_date->format('d M Y') : '')
            ->line('Log in to view the details and submit your work.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New homework assigned',
            'body' => $this->homework->title,
            'url' => route('admin.homework.show', $this->homework),
            'homework_id' => $this->homework->id,
        ];
    }
}
