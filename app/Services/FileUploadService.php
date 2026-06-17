<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Store an uploaded file on the public disk and return its relative path.
     */
    public function store(UploadedFile $file, string $directory): string
    {
        $name = Str::uuid().'.'.strtolower($file->getClientOriginalExtension());

        return $file->storeAs($directory, $name, 'public');
    }

    /**
     * Replace an existing file with a new upload, deleting the old one.
     */
    public function replace(UploadedFile $file, string $directory, ?string $oldPath = null): string
    {
        $this->delete($oldPath);

        return $this->store($file, $directory);
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Map a mime type / extension to the study-material file type enum.
     */
    public function detectFileType(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return match (true) {
            $extension === 'pdf' => 'pdf',
            in_array($extension, ['doc', 'docx', 'txt', 'rtf']) => 'doc',
            in_array($extension, ['ppt', 'pptx']) => 'ppt',
            in_array($extension, ['zip', 'rar', '7z']) => 'zip',
            in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']) => 'image',
            in_array($extension, ['mp4', 'mkv', 'avi', 'mov', 'webm']) => 'video',
            default => 'doc',
        };
    }
}
