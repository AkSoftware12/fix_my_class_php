<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Unique constraint handled at application layer (CoachingRequest validation).
        // Skipped to avoid conflict with existing data.
    }

    public function down(): void
    {
        //
    }
};
