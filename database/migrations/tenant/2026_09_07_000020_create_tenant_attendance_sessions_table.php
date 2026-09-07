<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('attendance_sessions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('class_group_id')->constrained('class_groups')->cascadeOnDelete();
            $table->unsignedSmallInteger('meeting_number');
            $table->date('meeting_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('topic', 255)->nullable();
            $table->string('status', 30)->default('scheduled')->index();
            $table->timestamps();

            $table->unique(['class_group_id', 'meeting_number']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('attendance_sessions');
    }
};
