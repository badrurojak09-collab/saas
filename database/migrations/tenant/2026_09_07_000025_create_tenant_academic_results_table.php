<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('academic_results', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignUuid('semester_id')->constrained('semesters')->restrictOnDelete();
            $table->decimal('total_credits', 4, 1)->default(0.0);
            $table->decimal('total_quality_points', 6, 2)->default(0.00);
            $table->decimal('semester_gpa', 4, 2)->default(0.00);
            $table->decimal('cumulative_gpa', 4, 2)->default(0.00);
            $table->string('status', 30)->default('draft')->index();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('academic_results');
    }
};
