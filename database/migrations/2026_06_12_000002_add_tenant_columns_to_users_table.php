<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile', 20)->nullable()->after('email');
            $table->foreignId('city_id')->nullable()->after('password')->constrained('cities')->nullOnDelete();
            $table->foreignId('coaching_id')->nullable()->after('city_id')->constrained('coachings')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->after('coaching_id')->constrained('branches')->nullOnDelete();
            $table->string('avatar_path')->nullable()->after('branch_id');
            $table->boolean('is_active')->default(true)->after('avatar_path')->index();
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->softDeletes();

            $table->index(['coaching_id', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
            $table->dropConstrainedForeignId('coaching_id');
            $table->dropConstrainedForeignId('branch_id');
            $table->dropColumn(['mobile', 'avatar_path', 'is_active', 'last_login_at', 'deleted_at']);
        });
    }
};
