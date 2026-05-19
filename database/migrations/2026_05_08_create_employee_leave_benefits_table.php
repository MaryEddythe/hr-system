<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_leave_benefits', function (Blueprint $table) {
            $table->id();

            // Foreign Key to Employees
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');

            // Employee Details (stored for record purposes)
            $table->string('name');
            $table->string('division');
            $table->string('position');
            $table->enum('employment_type', ['COS', 'PERMANENT'])->default('COS');

            // Leave Benefit Information
            $table->string('credit_type'); // e.g., Vacation, Sick Leave, Bereavement, etc.
            $table->date('start_date'); // Date benefit starts
            $table->date('end_date')->nullable(); // Date benefit ends
            $table->integer('credit_hours')->default(0); // Number of hours/days credited
            $table->integer('hours_used')->default(0); // Hours already used
            $table->integer('hours_remaining')->nullable(); // Computed remaining hours

            // Status
            $table->enum('status', ['ACTIVE', 'INACTIVE', 'PENDING', 'EXPIRED'])->default('ACTIVE');

            // Additional Notes
            $table->text('remarks')->nullable();

            // System timestamps
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_leave_benefits');
    }
};
