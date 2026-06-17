<?php

namespace App\Listeners;

use App\Events\HomeworkAssigned;
use App\Notifications\HomeworkAssignedNotification;
use App\Support\ResolvesTargetUsers;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendHomeworkNotifications implements ShouldQueue
{
    use InteractsWithQueue, ResolvesTargetUsers;

    public function handle(HomeworkAssigned $event): void
    {
        $homework = $event->homework->loadMissing('targets');

        $users = $this->resolveTargetStudentUsers($homework->targets, $homework->coaching_id);

        if ($users->isNotEmpty()) {
            Notification::send($users, new HomeworkAssignedNotification($homework));
        }
    }
}
