<?php

namespace App\Services;

use App\Models\OnlineClass;
use App\Repositories\OnlineClassRepository;

class OnlineClassService
{
    public function __construct(protected OnlineClassRepository $classes)
    {
    }

    public function create(array $data): OnlineClass
    {
        return $this->classes->create(collect($data)->only([
            'coaching_id', 'branch_id', 'batch_id', 'teacher_id', 'title',
            'class_date', 'start_time', 'end_time', 'meeting_link', 'description', 'status',
        ])->all());
    }

    public function update(OnlineClass $onlineClass, array $data): OnlineClass
    {
        return $this->classes->update($onlineClass, collect($data)->only([
            'branch_id', 'batch_id', 'teacher_id', 'title',
            'class_date', 'start_time', 'end_time', 'meeting_link', 'description', 'status',
        ])->all());
    }

    public function delete(OnlineClass $onlineClass): void
    {
        $this->classes->delete($onlineClass);
    }
}
