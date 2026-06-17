<?php

namespace App\Services;

use App\Events\HomeworkAssigned;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\User;
use App\Repositories\HomeworkRepository;
use Illuminate\Support\Facades\DB;

class HomeworkService
{
    public function __construct(
        protected HomeworkRepository $homework,
        protected FileUploadService $files,
    ) {
    }

    public function create(array $data, User $creator): Homework
    {
        return DB::transaction(function () use ($data, $creator) {
            if (! empty($data['attachment'])) {
                $data['attachment_path'] = $this->files->store($data['attachment'], 'homework');
            }

            /** @var Homework $homework */
            $homework = $this->homework->create([
                'coaching_id' => $data['coaching_id'],
                'created_by' => $creator->id,
                'subject_id' => $data['subject_id'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'type' => $data['type'],
                'attachment_path' => $data['attachment_path'] ?? null,
                'visibility' => $data['visibility'],
                'due_date' => $data['due_date'] ?? null,
                'status' => $data['status'] ?? 'pending',
            ]);

            $this->syncTargets($homework, $data['targets'] ?? []);

            HomeworkAssigned::dispatch($homework);

            return $homework;
        });
    }

    public function update(Homework $homework, array $data): Homework
    {
        return DB::transaction(function () use ($homework, $data) {
            if (! empty($data['attachment'])) {
                $data['attachment_path'] = $this->files->replace($data['attachment'], 'homework', $homework->attachment_path);
            }

            $homework = $this->homework->update($homework, collect($data)->only([
                'subject_id', 'title', 'description', 'type',
                'attachment_path', 'visibility', 'due_date', 'status',
            ])->all());

            if (array_key_exists('targets', $data)) {
                $this->syncTargets($homework, $data['targets'] ?? []);
            }

            return $homework;
        });
    }

    public function delete(Homework $homework): void
    {
        $this->homework->delete($homework);
    }

    public function reviewSubmission(HomeworkSubmission $submission, array $data, User $reviewer): HomeworkSubmission
    {
        $submission->fill([
            'status' => $data['status'],
            'remarks' => $data['remarks'] ?? null,
            'feedback' => $data['feedback'] ?? null,
            'marks' => $data['marks'] ?? null,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ])->save();

        $this->refreshHomeworkStatus($submission->homework);

        return $submission;
    }

    /**
     * Targets arrive as ["branch:3", "batch:7", "student:12", "coaching:1"].
     */
    protected function syncTargets(Homework $homework, array $targets): void
    {
        $homework->targets()->delete();

        foreach ($targets as $target) {
            [$type, $id] = array_pad(explode(':', $target, 2), 2, null);

            if (! in_array($type, ['coaching', 'branch', 'class', 'batch', 'student']) || ! is_numeric($id)) {
                continue;
            }

            $homework->targets()->create([
                'target_type' => $type,
                'target_id' => (int) $id,
            ]);
        }
    }

    protected function refreshHomeworkStatus(Homework $homework): void
    {
        $statuses = $homework->submissions()->pluck('status');

        if ($statuses->isEmpty()) {
            return;
        }

        $homework->status = match (true) {
            $statuses->every(fn ($s) => $s === 'completed') => 'completed',
            $statuses->contains('reviewed') => 'reviewed',
            default => 'submitted',
        };
        $homework->save();
    }
}
