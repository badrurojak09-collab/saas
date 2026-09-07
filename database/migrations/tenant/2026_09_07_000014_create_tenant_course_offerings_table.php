<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('course_offerings', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('semester_id')->constrained('semesters')->restrictOnDelete();
            $table->foreignUuid('curriculum_id')->constrained('curriculums')->restrictOnDelete();
            $table->foreignUuid('course_id')->constrained('courses')->restrictOnDelete();
            $table->foreignUuid('study_program_id')->constrained('study_programs')->restrictOnDelete();
            $table->string('code', 50);
            $table->string('class_type', 30)->default('regular')->index();
            $table->unsignedSmallInteger('capacity')->default(40);
            $table->string('status', 30)->default('active')->index();
            $table->timestamps();
            $table->softDeletes()->index();

            $table->unique(['semester_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('course_offerings');
    }
};
