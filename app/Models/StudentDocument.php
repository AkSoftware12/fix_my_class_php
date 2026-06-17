<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class StudentDocument extends Model
{
    protected $fillable = ['student_id', 'title', 'file_path', 'mime_type', 'size'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
