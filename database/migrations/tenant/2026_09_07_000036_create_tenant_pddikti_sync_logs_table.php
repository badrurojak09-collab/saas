<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('pddikti_sync_logs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('entity_type', 50)->index();
            $table->uuid('entity_id')->nullable()->index();
            $table->string('operation', 30)->default('insert');
            $table->string('request_id', 100)->nullable();
            $table->string('status', 30)->default('pending')->index();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('pddikti_sync_logs');
    }
};
