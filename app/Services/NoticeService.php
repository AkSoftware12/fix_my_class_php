<?php

namespace App\Services;

use App\Events\NoticePublished;
use App\Models\Notice;
use App\Models\User;
use App\Repositories\NoticeRepository;
use Illuminate\Support\Facades\DB;

class NoticeService
{
    public function __construct(
        protected NoticeRepository $notices,
        protected FileUploadService $files,
    ) {
    }

    public function create(array $data, User $creator): Notice
    {
        return DB::transaction(function () use ($data, $creator) {
            if (! empty($data['attachment'])) {
                $data['attachment_path'] = $this->files->store($data['attachment'], 'notices');
            }

            /** @var Notice $notice */
            $notice = $this->notices->create([
                'coaching_id' => $data['coaching_id'] ?? null,
                'created_by' => $creator->id,
                'title' => $data['title'],
                'body' => $data['body'],
                'type' => $data['type'],
                'visibility' => $data['visibility'],
                'audience' => $data['audience'] ?? 'all',
                'attachment_path' => $data['attachment_path'] ?? null,
                'publish_at' => $data['publish_at'] ?? now(),
                'expires_at' => $data['expires_at'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            $this->syncTargets($notice, $data['targets'] ?? []);

            if ($notice->is_active && $notice->publish_at <= now()) {
                NoticePublished::dispatch($notice);
            }

            return $notice;
        });
    }

    public function update(Notice $notice, array $data): Notice
    {
        return DB::transaction(function () use ($notice, $data) {
            if (! empty($data['attachment'])) {
                $data['attachment_path'] = $this->files->replace($data['attachment'], 'notices', $notice->attachment_path);
            }

            $notice = $this->notices->update($notice, collect($data)->only([
                'title', 'body', 'type', 'visibility', 'audience',
                'attachment_path', 'publish_at', 'expires_at', 'is_active',
            ])->all());

            if (array_key_exists('targets', $data)) {
                $this->syncTargets($notice, $data['targets'] ?? []);
            }

            return $notice;
        });
    }

    public function delete(Notice $notice): void
    {
        $this->notices->delete($notice);
    }

    public function markRead(Notice $notice, User $user): void
    {
        $notice->reads()->firstOrCreate(
            ['user_id' => $user->id],
            ['read_at' => now()],
        );
    }

    protected function syncTargets(Notice $notice, array $targets): void
    {
        $notice->targets()->delete();

        foreach ($targets as $target) {
            [$type, $id] = array_pad(explode(':', $target, 2), 2, null);

            if (! in_array($type, ['coaching', 'branch', 'class', 'batch', 'student']) || ! is_numeric($id)) {
                continue;
            }

            $notice->targets()->create([
                'target_type' => $type,
                'target_id' => (int) $id,
            ]);
        }
    }
}
