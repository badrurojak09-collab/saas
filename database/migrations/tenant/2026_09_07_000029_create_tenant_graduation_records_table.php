<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('graduation_records', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignUuid('study_program_id')->constrained('study_programs')->restrictOnDelete();
            $table->string('graduation_number', 100)->unique();
            $table->date('graduation_date');
            $table->string('graduation_period', 50)->nullable();
            $table->decimal('final_gpa', 4, 2)->default(0.00);
            $table->decimal('total_credits', 4, 1)->default(0.0);
            $table->string('status', 30)->default('proposed')->index();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('graduation_records');
    }
};
