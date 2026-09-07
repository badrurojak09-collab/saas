<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('class_lecturers', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('class_group_id')->constrained('class_groups')->cascadeOnDelete();
            $table->foreignUuid('lecturer_id')->constrained('lecturers')->restrictOnDelete();
            $table->string('role', 30)->default('primary');
            $table->timestamps();

            $table->unique(['class_group_id', 'lecturer_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('class_lecturers');
    }
};
