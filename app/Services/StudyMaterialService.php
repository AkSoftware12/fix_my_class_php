<?php

namespace App\Services;

use App\Models\StudyMaterial;
use App\Models\User;
use App\Repositories\StudyMaterialRepository;
use Illuminate\Support\Facades\DB;

class StudyMaterialService
{
    public function __construct(
        protected StudyMaterialRepository $materials,
        protected FileUploadService $files,
    ) {
    }

    public function create(array $data, User $uploader): StudyMaterial
    {
        return DB::transaction(function () use ($data, $uploader) {
            $file = $data['file'];

            /** @var StudyMaterial $material */
            $material = $this->materials->create([
                'coaching_id' => $data['coaching_id'],
                'uploaded_by' => $uploader->id,
                'subject_id' => $data['subject_id'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'file_type' => $this->files->detectFileType($file),
                'file_path' => $this->files->store($file, 'study-materials'),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize() ?: 0,
                'is_active' => $data['is_active'] ?? true,
            ]);

            $this->syncTargets($material, $data['targets'] ?? []);

            return $material;
        });
    }

    public function update(StudyMaterial $material, array $data): StudyMaterial
    {
        return DB::transaction(function () use ($material, $data) {
            $payload = collect($data)->only(['subject_id', 'title', 'description', 'is_active'])->all();

            if (! empty($data['file'])) {
                $file = $data['file'];
                $payload['file_type'] = $this->files->detectFileType($file);
                $payload['file_path'] = $this->files->replace($file, 'study-materials', $material->file_path);
                $payload['mime_type'] = $file->getClientMimeType();
                $payload['size'] = $file->getSize() ?: 0;
            }

            $material = $this->materials->update($material, $payload);

            if (array_key_exists('targets', $data)) {
                $this->syncTargets($material, $data['targets'] ?? []);
            }

            return $material;
        });
    }

    public function delete(StudyMaterial $material): void
    {
        $this->materials->delete($material);
    }

    public function recordDownload(StudyMaterial $material): StudyMaterial
    {
        $material->increment('download_count');

        return $material;
    }

    protected function syncTargets(StudyMaterial $material, array $targets): void
    {
        $material->targets()->delete();

        foreach ($targets as $target) {
            [$type, $id] = array_pad(explode(':', $target, 2), 2, null);

            if (! in_array($type, ['coaching', 'branch', 'class', 'batch', 'student']) || ! is_numeric($id)) {
                continue;
            }

            $material->targets()->create([
                'target_type' => $type,
                'target_id' => (int) $id,
            ]);
        }
    }
}
