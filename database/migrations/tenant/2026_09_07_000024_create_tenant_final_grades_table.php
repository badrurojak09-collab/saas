<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('final_grades', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('class_group_id')->constrained('class_groups')->cascadeOnDelete();
            $table->foreignUuid('student_id')->constrained('students')->restrictOnDelete();
            $table->decimal('numeric_score', 6, 2)->default(0.00);
            $table->string('letter_grade', 5)->default('E');
            $table->decimal('grade_point', 4, 2)->default(0.00);
            $table->string('status', 30)->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['class_group_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('final_grades');
    }
};
