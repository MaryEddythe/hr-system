<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add division_id, keep old department column for now as nullable
            $table->foreignId('division_id')
                  ->nullable()
                  ->after('email')
                  ->constrained('divisions')
                  ->nullOnDelete();

            // Make old department nullable (don't drop it yet, safe migration)
            $table->string('department')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropColumn('division_id');
        });
    }
};