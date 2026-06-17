<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homework', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coaching_id')->constrained('coachings')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->string('title', 200);
            $table->longText('description')->nullable();
            $table->enum('type', ['text', 'pdf', 'image', 'video'])->default('text');
            $table->string('attachment_path')->nullable();
            $table->enum('visibility', ['public', 'private'])->default('private');
            $table->date('due_date')->nullable()->index();
            $table->enum('status', ['pending', 'submitted', 'reviewed', 'completed'])->default('pending')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['coaching_id', 'status']);
        });

        Schema::create('homework_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('homework_id')->constrained('homework')->cascadeOnDelete();
            $table->enum('target_type', ['coaching', 'branch', 'class', 'batch', 'student']);
            $table->unsignedBigInteger('target_id');
            $table->timestamps();

            $table->unique(['homework_id', 'target_type', 'target_id'], 'homework_targets_unique');
            $table->index(['target_type', 'target_id']);
        });

        Schema::create('homework_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('homework_id')->constrained('homework')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->longText('answer_text')->nullable();
            $table->string('attachment_path')->nullable();
            $table->enum('status', ['submitted', 'reviewed', 'completed'])->default('submitted')->index();
            $table->text('remarks')->nullable();
            $table->text('feedback')->nullable();
            $table->unsignedTinyInteger('marks')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(['homework_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homework_submissions');
        Schema::dropIfExists('homework_targets');
        Schema::dropIfExists('homework');
    }
};
