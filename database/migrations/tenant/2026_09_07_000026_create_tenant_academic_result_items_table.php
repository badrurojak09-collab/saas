<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('academic_result_items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('academic_result_id')->constrained('academic_results')->cascadeOnDelete();
            $table->foreignUuid('course_id')->constrained('courses')->restrictOnDelete();
            $table->foreignUuid('class_group_id')->nullable()->constrained('class_groups')->nullOnDelete();
            $table->decimal('credit_units', 4, 1)->default(3.0);
            $table->decimal('numeric_score', 6, 2)->default(0.00);
            $table->string('letter_grade', 5)->default('E');
            $table->decimal('grade_point', 4, 2)->default(0.00);
            $table->decimal('quality_points', 6, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['academic_result_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('academic_result_items');
    }
};
