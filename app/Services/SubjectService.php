<?php

namespace App\Services;

use App\Models\Subject;
use App\Repositories\SubjectRepository;

class SubjectService
{
    public function __construct(protected SubjectRepository $subjects)
    {
    }

    public function create(array $data): Subject
    {
        return $this->subjects->create(collect($data)->only([
            'coaching_id', 'name', 'code', 'is_active',
        ])->all());
    }

    public function update(Subject $subject, array $data): Subject
    {
        return $this->subjects->update($subject, collect($data)->only([
            'coaching_id', 'name', 'code', 'is_active',
        ])->all());
    }

    public function delete(Subject $subject): void
    {
        $this->subjects->delete($subject);
    }
}
