<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('pddikti_mappings', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('entity_type', 50);
            $table->uuid('entity_id');
            $table->string('external_id', 100)->nullable()->index();
            $table->string('external_code', 100)->nullable();
            $table->string('sync_status', 30)->default('pending')->index();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('pddikti_mappings');
    }
};
