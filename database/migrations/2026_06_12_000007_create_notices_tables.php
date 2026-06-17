<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coaching_id')->nullable()->constrained('coachings')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title', 200);
            $table->longText('body');
            $table->enum('type', ['general', 'holiday', 'event', 'exam', 'urgent'])->default('general')->index();
            $table->enum('visibility', ['public', 'private'])->default('private');
            $table->enum('audience', ['all', 'teachers', 'students'])->default('all');
            $table->string('attachment_path')->nullable();
            $table->timestamp('publish_at')->nullable()->index();
            $table->date('expires_at')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['coaching_id', 'is_active']);
        });

        Schema::create('notice_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notice_id')->constrained('notices')->cascadeOnDelete();
            $table->enum('target_type', ['coaching', 'branch', 'class', 'batch', 'student']);
            $table->unsignedBigInteger('target_id');
            $table->timestamps();

            $table->unique(['notice_id', 'target_type', 'target_id'], 'notice_targets_unique');
            $table->index(['target_type', 'target_id']);
        });

        Schema::create('notice_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notice_id')->constrained('notices')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('read_at');

            $table->unique(['notice_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notice_reads');
        Schema::dropIfExists('notice_targets');
        Schema::dropIfExists('notices');
    }
};
