<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::connection('landlord')->create('tenant_database_operations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_database_id');
            $table->string('operation', 50);
            $table->string('status', 30);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table
                ->foreign('tenant_database_id')
                ->references('id')
                ->on('tenant_databases')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('tenant_database_operations');
    }
};
