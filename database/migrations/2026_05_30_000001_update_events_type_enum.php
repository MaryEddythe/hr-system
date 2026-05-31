<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update the enum to only include the three types
        Schema::table('events', function (Blueprint $table) {
            // For MySQL
            DB::statement("ALTER TABLE events MODIFY type ENUM('Travel Order', 'Event', 'Birthday') DEFAULT 'Event'");
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Rollback to original enum
            DB::statement("ALTER TABLE events MODIFY type ENUM('Travel Order', 'Event', 'Birthday', 'Task') DEFAULT 'Event'");
        });
    }
};
