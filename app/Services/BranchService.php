<?php

namespace App\Services;

use App\Models\Branch;
use App\Repositories\BranchRepository;

class BranchService
{
    public function __construct(protected BranchRepository $branches)
    {
    }

    public function create(array $data): Branch
    {
        return $this->branches->create(collect($data)->only([
            'coaching_id', 'name', 'code', 'address', 'latitude', 'longitude', 'contact_number', 'is_active',
        ])->all());
    }

    public function update(Branch $branch, array $data): Branch
    {
        return $this->branches->update($branch, collect($data)->only([
            'coaching_id', 'name', 'code', 'address', 'latitude', 'longitude', 'contact_number', 'is_active',
        ])->all());
    }

    public function delete(Branch $branch): void
    {
        $this->branches->delete($branch);
    }
}
