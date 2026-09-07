<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('study_plan_items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('study_plan_id')->constrained('study_plans')->cascadeOnDelete();
            $table->foreignUuid('class_group_id')->constrained('class_groups')->restrictOnDelete();
            $table->foreignUuid('course_id')->constrained('courses')->restrictOnDelete();
            $table->decimal('credit_units', 4, 1)->default(3.0);
            $table->string('status', 30)->default('draft')->index();
            $table->timestamps();

            $table->unique(['study_plan_id', 'class_group_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('study_plan_items');
    }
};
