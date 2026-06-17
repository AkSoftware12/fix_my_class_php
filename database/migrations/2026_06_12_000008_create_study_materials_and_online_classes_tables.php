<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coaching_id')->constrained('coachings')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->enum('file_type', ['pdf', 'doc', 'ppt', 'zip', 'image', 'video'])->index();
            $table->string('file_path');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('download_count')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['coaching_id', 'is_active']);
        });

        Schema::create('study_material_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_material_id')->constrained('study_materials')->cascadeOnDelete();
            $table->enum('target_type', ['coaching', 'branch', 'class', 'batch', 'student']);
            $table->unsignedBigInteger('target_id');
            $table->timestamps();

            $table->unique(['study_material_id', 'target_type', 'target_id'], 'study_material_targets_unique');
            $table->index(['target_type', 'target_id']);
        });

        Schema::create('online_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coaching_id')->constrained('coachings')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('batches')->nullOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('title', 200);
            $table->date('class_date')->index();
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->string('meeting_link', 500);
            $table->text('description')->nullable();
            $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled'])->default('scheduled')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['coaching_id', 'class_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_classes');
        Schema::dropIfExists('study_material_targets');
        Schema::dropIfExists('study_materials');
    }
};
