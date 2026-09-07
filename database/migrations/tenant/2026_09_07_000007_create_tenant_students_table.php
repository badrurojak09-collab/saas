<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('students', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('student_number', 50)->unique();
            $table->string('national_student_number', 50)->nullable()->unique();
            $table->foreignUuid('study_program_id')->constrained('study_programs')->restrictOnDelete();
            $table->string('entry_year', 10)->index();
            $table->foreignUuid('entry_semester_id')->nullable()->constrained('semesters')->nullOnDelete();
            $table->string('admission_type', 50)->nullable();
            $table->string('name', 150);
            $table->string('gender', 10)->nullable();
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('nik', 30)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 190)->nullable();
            $table->text('address')->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->date('graduation_date')->nullable();
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('students');
    }
};
