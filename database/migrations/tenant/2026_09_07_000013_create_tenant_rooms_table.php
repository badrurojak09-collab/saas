<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('rooms', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('building_id')->constrained('buildings')->restrictOnDelete();
            $table->string('code', 50);
            $table->string('name', 150);
            $table->string('room_type', 30)->default('classroom')->index();
            $table->unsignedSmallInteger('capacity')->default(40);
            $table->string('status', 30)->default('active')->index();
            $table->timestamps();
            $table->softDeletes()->index();

            $table->unique(['building_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('rooms');
    }
};
