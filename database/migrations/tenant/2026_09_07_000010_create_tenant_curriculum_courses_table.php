<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('curriculum_courses', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('curriculum_id')->constrained('curriculums')->cascadeOnDelete();
            $table->foreignUuid('course_id')->constrained('courses')->restrictOnDelete();
            $table->unsignedSmallInteger('semester_number')->default(1);
            $table->string('course_group', 50)->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->decimal('credit_units', 4, 1)->default(3.0);
            $table->string('minimum_grade', 5)->default('C');
            $table->timestamps();

            $table->unique(['curriculum_id', 'course_id']);
            $table->index(['curriculum_id', 'semester_number']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('curriculum_courses');
    }
};
