<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->enum('file_type', ['PDS', 'SALN', 'NBI Clearance', 'Medical Certificate', 'PhilHealth', 'SSS', 'Pag-IBIG']);

            $table->string('file_name');
            $table->string('file_url')->nullable();
            $table->string('file_id')->nullable();

            $table->timestamps();

            // Prevent duplicate uploads of the same file type per employee
            $table->unique(['employee_id', 'file_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_files');
    }
};

