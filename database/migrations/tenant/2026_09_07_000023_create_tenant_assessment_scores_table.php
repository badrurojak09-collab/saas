<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('assessment_scores', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('assessment_component_id')->constrained('assessment_components')->cascadeOnDelete();
            $table->foreignUuid('student_id')->constrained('students')->restrictOnDelete();
            $table->decimal('score', 6, 2)->default(0.00);
            $table->foreignUuid('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('graded_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['assessment_component_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('assessment_scores');
    }
};
