<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE employee_leave_benefits MODIFY credit_type VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE employee_leave_benefits MODIFY credit_type ENUM('Vacation Leave','Sick Leave','Wellness Leave','Special Privilege Leave','Maternity Leave','Paternity Leave','Solo Parent Leave','Rehabilitation Leave','Special Emergency Leave','Credited Time-Off') NULL");
    }
};
