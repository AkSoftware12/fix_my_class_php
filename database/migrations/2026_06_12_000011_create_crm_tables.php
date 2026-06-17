<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coaching_id')->constrained('coachings')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('student_name', 120);
            $table->string('guardian_name', 120)->nullable();
            $table->string('mobile', 20);
            $table->string('email', 180)->nullable();
            $table->string('interested_class', 120)->nullable();
            $table->string('source', 60)->nullable();
            $table->enum('stage', ['new', 'follow_up', 'demo_class', 'admission_done', 'rejected'])->default('new')->index();
            $table->date('next_follow_up_at')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['coaching_id', 'stage']);
        });

        Schema::create('lead_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_lead_id')->constrained('admission_leads')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('stage', ['new', 'follow_up', 'demo_class', 'admission_done', 'rejected']);
            $table->text('remarks')->nullable();
            $table->timestamp('followed_up_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_follow_ups');
        Schema::dropIfExists('admission_leads');
    }
};
