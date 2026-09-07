<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::connection('landlord')->create('tenant_migration_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_database_id');
            $table->string('migration', 255);
            $table->unsignedInteger('batch');
            $table->timestamp('applied_at');
            $table->unsignedInteger('execution_time_ms')->nullable();

            $table
                ->foreign('tenant_database_id')
                ->references('id')
                ->on('tenant_databases')
                ->cascadeOnDelete();

            $table->unique(['tenant_database_id', 'migration']);
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('tenant_migration_versions');
    }
};
