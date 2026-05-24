<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('employee_leave_history')) {
            return;
        }

        Schema::create('employee_leave_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('leave_benefit_id')->constrained('employee_leave_benefits')->onDelete('cascade');
            $table->string('credit_type');
            $table->integer('credits_added');
            $table->integer('hours_used')->default(0);
            $table->integer('hours_remaining')->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_leave_history');
    }
};
