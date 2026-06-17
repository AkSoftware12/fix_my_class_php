<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Teacher;
use App\Repositories\TeacherRepository;
use Illuminate\Support\Facades\DB;

class TeacherService
{
    public function __construct(
        protected TeacherRepository $teachers,
        protected UserService $userService,
    ) {
    }

    public function create(array $data): Teacher
    {
        return DB::transaction(function () use ($data) {
            $branch = Branch::findOrFail($data['branch_id']);

            $user = $this->userService->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'] ?? null,
                'password' => $data['password'],
                'coaching_id' => $branch->coaching_id,
                'branch_id' => $branch->id,
                'is_active' => $data['is_active'] ?? true,
                'role' => 'Teacher',
            ]);

            return $this->teachers->create([
                'user_id' => $user->id,
                'coaching_id' => $branch->coaching_id,
                'branch_id' => $branch->id,
                'subject_id' => $data['subject_id'] ?? null,
                'qualification' => $data['qualification'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);
        });
    }

    public function update(Teacher $teacher, array $data): Teacher
    {
        return DB::transaction(function () use ($teacher, $data) {
            $branch = Branch::findOrFail($data['branch_id']);

            $this->userService->update($teacher->user, [
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'] ?? null,
                'password' => $data['password'] ?? null,
                'coaching_id' => $branch->coaching_id,
                'branch_id' => $branch->id,
                'is_active' => $data['is_active'] ?? $teacher->user->is_active,
            ]);

            return $this->teachers->update($teacher, [
                'coaching_id' => $branch->coaching_id,
                'branch_id' => $branch->id,
                'subject_id' => $data['subject_id'] ?? null,
                'qualification' => $data['qualification'] ?? null,
                'is_active' => $data['is_active'] ?? $teacher->is_active,
            ]);
        });
    }

    public function delete(Teacher $teacher): void
    {
        DB::transaction(function () use ($teacher) {
            $this->teachers->delete($teacher);
            $teacher->user?->delete();
        });
    }
}
