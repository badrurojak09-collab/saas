<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::connection('landlord')->create('tenant_databases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name', 100);
            $table->string('driver', 30)->default('mysql');
            $table->string('host', 255);
            $table->unsignedSmallInteger('port')->default(3306);
            $table->string('database', 150);
            $table->string('username', 150);
            $table->text('password')->nullable();
            $table->string('status', 30)->default('provisioning');
            $table->string('schema_version', 50)->nullable();
            $table->boolean('is_primary')->default(true);
            $table->timestamp('last_migrated_at')->nullable();
            $table->timestamp('last_backup_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table
                ->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->restrictOnDelete();

            $table->unique(['tenant_id', 'name']);
            $table->index('status');
            $table->index('schema_version');
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('tenant_databases');
    }
};
