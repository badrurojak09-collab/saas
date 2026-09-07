<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('class_schedules', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('class_group_id')->constrained('class_groups')->cascadeOnDelete();
            $table->foreignUuid('room_id')->constrained('rooms')->restrictOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 1=Monday, 7=Sunday
            $table->time('start_time');
            $table->time('end_time');
            $table->string('meeting_type', 30)->default('lecture')->index();
            $table->string('status', 30)->default('active')->index();
            $table->timestamps();

            $table->index(['room_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('class_schedules');
    }
};
