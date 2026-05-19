<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Employee Identifier
            $table->string('employee_id')->unique(); // EMP-0001

            // Basic Info
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique()->index();

            // Job Info
            $table->string('department');
            $table->string('position');
            $table->date('hired_at')->nullable();

            // Employment Type (COS / PERMANENT)
            $table->enum('employment_type', ['COS', 'PERMANENT'])->default('COS');

            // Google Drive Integration
            $table->string('drive_folder_id')->nullable();
            $table->string('drive_folder_url')->nullable();

            // System timestamps
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};