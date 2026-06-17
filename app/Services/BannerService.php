<?php

namespace App\Services;

use App\Models\Banner;
use App\Repositories\BannerRepository;

class BannerService
{
    public function __construct(
        protected BannerRepository $banners,
        protected FileUploadService $files,
    ) {
    }

    public function create(array $data): Banner
    {
        if (! empty($data['image'])) {
            $data['image_path'] = $this->files->store($data['image'], 'banners');
        }

        return $this->banners->create(collect($data)->only([
            'coaching_id', 'title', 'image_path', 'url',
            'starts_at', 'ends_at', 'sort_order', 'is_active',
        ])->all());
    }

    public function update(Banner $banner, array $data): Banner
    {
        if (! empty($data['image'])) {
            $data['image_path'] = $this->files->replace($data['image'], 'banners', $banner->image_path);
        }

        return $this->banners->update($banner, collect($data)->only([
            'coaching_id', 'title', 'image_path', 'url',
            'starts_at', 'ends_at', 'sort_order', 'is_active',
        ])->all());
    }

    public function delete(Banner $banner): void
    {
        if ($banner->image_path) {
            $this->files->delete($banner->image_path);
        }
        $this->banners->delete($banner);
    }
}
