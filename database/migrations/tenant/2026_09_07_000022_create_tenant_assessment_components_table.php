<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('assessment_components', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('class_group_id')->constrained('class_groups')->cascadeOnDelete();
            $table->string('code', 50);
            $table->string('name', 100);
            $table->decimal('weight', 5, 2);
            $table->decimal('max_score', 6, 2)->default(100.00);
            $table->string('assessment_type', 30)->default('assignment')->index();
            $table->unsignedSmallInteger('sequence')->default(1);
            $table->timestamps();

            $table->unique(['class_group_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('assessment_components');
    }
};
