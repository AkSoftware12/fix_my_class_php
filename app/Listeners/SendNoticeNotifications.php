<?php

namespace App\Listeners;

use App\Events\NoticePublished;
use App\Models\User;
use App\Notifications\NoticePublishedNotification;
use App\Support\ResolvesTargetUsers;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendNoticeNotifications implements ShouldQueue
{
    use InteractsWithQueue, ResolvesTargetUsers;

    public function handle(NoticePublished $event): void
    {
        $notice = $event->notice->loadMissing('targets');

        $users = collect();

        if (in_array($notice->audience, ['all', 'students'])) {
            $users = $users->merge(
                $this->resolveTargetStudentUsers($notice->targets, $notice->coaching_id),
            );
        }

        if (in_array($notice->audience, ['all', 'teachers']) && $notice->coaching_id) {
            $teachers = User::query()
                ->active()
                ->role('Teacher')
                ->where('coaching_id', $notice->coaching_id)
                ->get();

            $users = $users->merge($teachers);
        }

        $users = $users->unique('id')->values();

        if ($users->isNotEmpty()) {
            Notification::send($users, new NoticePublishedNotification($notice));
        }
    }
}
