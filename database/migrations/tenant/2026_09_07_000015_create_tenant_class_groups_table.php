<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('class_groups', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('course_offering_id')->constrained('course_offerings')->cascadeOnDelete();
            $table->string('code', 50);
            $table->string('name', 100);
            $table->unsignedSmallInteger('capacity')->default(40);
            $table->string('status', 30)->default('active')->index();
            $table->timestamps();
            $table->softDeletes()->index();

            $table->unique(['course_offering_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('class_groups');
    }
};
