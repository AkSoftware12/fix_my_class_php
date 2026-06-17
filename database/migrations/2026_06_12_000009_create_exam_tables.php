<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coaching_id')->constrained('coachings')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $table->foreignId('school_class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('batches')->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title', 200);
            $table->enum('type', ['mcq', 'subjective', 'assignment'])->default('mcq')->index();
            $table->text('instructions')->nullable();
            $table->date('exam_date')->nullable()->index();
            $table->time('start_time')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->unsignedSmallInteger('total_marks')->default(100);
            $table->unsignedSmallInteger('passing_marks')->default(33);
            $table->enum('status', ['draft', 'published', 'completed', 'cancelled'])->default('draft')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['coaching_id', 'status']);
        });

        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->text('question');
            $table->enum('type', ['mcq', 'subjective'])->default('mcq');
            $table->json('options')->nullable();
            $table->string('correct_option', 10)->nullable();
            $table->unsignedSmallInteger('marks')->default(1);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->decimal('marks_obtained', 6, 2)->default(0);
            $table->string('grade', 5)->nullable();
            $table->unsignedInteger('rank')->nullable();
            $table->boolean('is_pass')->default(false);
            $table->text('remarks')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['exam_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exams');
    }
};
