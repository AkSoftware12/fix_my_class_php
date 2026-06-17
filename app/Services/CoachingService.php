<?php

namespace App\Services;

use App\Models\Coaching;
use App\Repositories\CoachingRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CoachingService
{
    public function __construct(
        protected CoachingRepository $coachings,
        protected FileUploadService $files,
    ) {
    }

    public function create(array $data): Coaching
    {
        return DB::transaction(function () use ($data) {
            if (! empty($data['logo'])) {
                $data['logo_path'] = $this->files->store($data['logo'], 'coaching-logos');
            }

            $data['slug'] = $this->uniqueSlug($data['name']);

            return $this->coachings->create(collect($data)->only([
                'city_id', 'name', 'slug', 'owner_name', 'email',
                'mobile', 'address', 'latitude', 'longitude', 'logo_path', 'is_active',
            ])->all());
        });
    }

    public function update(Coaching $coaching, array $data): Coaching
    {
        return DB::transaction(function () use ($coaching, $data) {
            if (! empty($data['logo'])) {
                $data['logo_path'] = $this->files->replace($data['logo'], 'coaching-logos', $coaching->logo_path);
            }

            if (isset($data['name']) && $data['name'] !== $coaching->name) {
                $data['slug'] = $this->uniqueSlug($data['name'], $coaching->id);
            }

            return $this->coachings->update($coaching, collect($data)->only([
                'city_id', 'name', 'slug', 'owner_name', 'email',
                'mobile', 'address', 'latitude', 'longitude', 'logo_path', 'is_active',
            ])->all());
        });
    }

    public function delete(Coaching $coaching): void
    {
        $this->coachings->delete($coaching);
    }

    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while ($this->coachings->query()
            ->withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
