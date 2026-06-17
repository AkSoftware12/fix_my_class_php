<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Student;
use App\Repositories\StudentRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class StudentService
{
    public function __construct(
        protected StudentRepository $students,
        protected UserService $userService,
        protected FileUploadService $files,
    ) {
    }

    public function create(array $data): Student
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
                'role' => 'Student',
            ]);

            $photoPath = ! empty($data['photo'])
                ? $this->files->store($data['photo'], 'student-photos')
                : null;

            $student = $this->students->create([
                'user_id' => $user->id,
                'coaching_id' => $branch->coaching_id,
                'branch_id' => $branch->id,
                'school_class_id' => $data['school_class_id'] ?? null,
                'batch_id' => $data['batch_id'] ?? null,
                'admission_number' => $data['admission_number']
                    ?? $this->students->nextAdmissionNumber($branch->coaching_id),
                'guardian_name' => $data['guardian_name'] ?? null,
                'guardian_mobile' => $data['guardian_mobile'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'photo_path' => $photoPath,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (! empty($data['batch_id'])) {
                $student->batches()->syncWithoutDetaching([$data['batch_id']]);
            }

            $this->storeDocuments($student, $data['documents'] ?? []);

            return $student;
        });
    }

    public function update(Student $student, array $data): Student
    {
        return DB::transaction(function () use ($student, $data) {
            $branch = Branch::findOrFail($data['branch_id']);

            $this->userService->update($student->user, [
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'] ?? null,
                'password' => $data['password'] ?? null,
                'coaching_id' => $branch->coaching_id,
                'branch_id' => $branch->id,
                'is_active' => $data['is_active'] ?? $student->user->is_active,
            ]);

            if (! empty($data['photo'])) {
                $data['photo_path'] = $this->files->replace($data['photo'], 'student-photos', $student->photo_path);
            }

            $student = $this->students->update($student, collect([
                'coaching_id' => $branch->coaching_id,
                'branch_id' => $branch->id,
                'school_class_id' => $data['school_class_id'] ?? null,
                'batch_id' => $data['batch_id'] ?? null,
                'guardian_name' => $data['guardian_name'] ?? null,
                'guardian_mobile' => $data['guardian_mobile'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'photo_path' => $data['photo_path'] ?? $student->photo_path,
                'is_active' => $data['is_active'] ?? $student->is_active,
            ])->all());

            if (! empty($data['batch_id'])) {
                $student->batches()->syncWithoutDetaching([$data['batch_id']]);
            }

            $this->storeDocuments($student, $data['documents'] ?? []);

            return $student;
        });
    }

    public function delete(Student $student): void
    {
        DB::transaction(function () use ($student) {
            $this->students->delete($student);
            $student->user?->delete();
        });
    }

    /**
     * @param  array<int, UploadedFile>  $documents
     */
    protected function storeDocuments(Student $student, array $documents): void
    {
        foreach ($documents as $document) {
            if (! $document instanceof UploadedFile) {
                continue;
            }

            $student->documents()->create([
                'title' => $document->getClientOriginalName(),
                'file_path' => $this->files->store($document, 'student-documents'),
                'mime_type' => $document->getClientMimeType(),
                'size' => $document->getSize() ?: 0,
            ]);
        }
    }
}
