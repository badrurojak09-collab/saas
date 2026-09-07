<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('departments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('faculty_id')->constrained('faculties')->restrictOnDelete();
            $table->string('code', 50);
            $table->string('name', 150);
            $table->string('short_name', 50)->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->timestamps();
            $table->softDeletes()->index();

            $table->unique(['faculty_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('departments');
    }
};
