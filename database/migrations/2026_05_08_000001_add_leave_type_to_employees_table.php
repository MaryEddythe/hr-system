<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('leave_type', [
                'Special Emergency Leave',
                'Rehabilitation Leave',
                'Solo Parent Leave',
                'Paternity Leave',
                'Maternity Leave',
                'Special Privilege Leave',
                'Wellness Leave',
                'Vacation Leave',
            ])->nullable()->after('employment_type');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('leave_type');
        });
    }
};
