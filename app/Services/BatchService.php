<?php

namespace App\Services;

use App\Models\Batch;
use App\Repositories\BatchRepository;
use Illuminate\Support\Facades\DB;

class BatchService
{
    public function __construct(protected BatchRepository $batches)
    {
    }

    public function create(array $data): Batch
    {
        return DB::transaction(function () use ($data) {
            /** @var Batch $batch */
            $batch = $this->batches->create(collect($data)->only([
                'coaching_id', 'branch_id', 'school_class_id', 'name',
                'start_time', 'end_time', 'capacity', 'is_active',
            ])->all());

            $this->syncAssignments($batch, $data);

            return $batch;
        });
    }

    public function update(Batch $batch, array $data): Batch
    {
        return DB::transaction(function () use ($batch, $data) {
            $batch = $this->batches->update($batch, collect($data)->only([
                'coaching_id', 'branch_id', 'school_class_id', 'name',
                'start_time', 'end_time', 'capacity', 'is_active',
            ])->all());

            $this->syncAssignments($batch, $data);

            return $batch;
        });
    }

    public function delete(Batch $batch): void
    {
        $this->batches->delete($batch);
    }

    protected function syncAssignments(Batch $batch, array $data): void
    {
        if (array_key_exists('subject_ids', $data)) {
            $batch->subjects()->sync($data['subject_ids'] ?? []);
        }

        if (array_key_exists('teacher_ids', $data)) {
            $batch->teachers()->sync($data['teacher_ids'] ?? []);
        }

        if (array_key_exists('student_ids', $data)) {
            $batch->students()->sync($data['student_ids'] ?? []);
        }
    }
}
