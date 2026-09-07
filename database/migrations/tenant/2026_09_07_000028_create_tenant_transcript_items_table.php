<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('transcript_items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('transcript_id')->constrained('transcripts')->cascadeOnDelete();
            $table->foreignUuid('course_id')->constrained('courses')->restrictOnDelete();
            $table->foreignUuid('semester_id')->constrained('semesters')->restrictOnDelete();
            $table->decimal('credit_units', 4, 1)->default(3.0);
            $table->string('letter_grade', 5)->default('E');
            $table->decimal('grade_point', 4, 2)->default(0.00);
            $table->decimal('quality_points', 6, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['transcript_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('transcript_items');
    }
};
