<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('courses', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->string('short_name', 50)->nullable();
            $table->text('description')->nullable();
            $table->decimal('credit_units', 4, 1)->default(3.0);
            $table->string('course_type', 30)->default('mandatory')->index();
            $table->string('course_category', 30)->default('core')->index();
            $table->string('grading_type', 30)->default('standard_letter')->index();
            $table->string('status', 30)->default('active')->index();
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('courses');
    }
};
