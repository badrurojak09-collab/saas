<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('curriculums', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('study_program_id')->constrained('study_programs')->restrictOnDelete();
            $table->string('code', 50);
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('effective_start_year', 10);
            $table->string('effective_end_year', 10)->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->timestamps();
            $table->softDeletes()->index();

            $table->unique(['study_program_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('curriculums');
    }
};
