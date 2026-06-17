<?php

namespace App\Services;

use App\Models\SchoolClass;
use App\Repositories\SchoolClassRepository;

class SchoolClassService
{
    public function __construct(protected SchoolClassRepository $classes)
    {
    }

    public function create(array $data): SchoolClass
    {
        return $this->classes->create(collect($data)->only([
            'coaching_id', 'branch_id', 'name', 'description', 'is_active',
        ])->all());
    }

    public function update(SchoolClass $class, array $data): SchoolClass
    {
        return $this->classes->update($class, collect($data)->only([
            'coaching_id', 'branch_id', 'name', 'description', 'is_active',
        ])->all());
    }

    public function delete(SchoolClass $class): void
    {
        $this->classes->delete($class);
    }
}
