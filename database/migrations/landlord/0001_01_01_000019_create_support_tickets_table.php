<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::connection('landlord')->create('support_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('ticket_number', 50);
            $table->string('subject', 255);
            $table->string('priority', 20);
            $table->string('status', 30);
            $table->uuid('created_by');
            $table->uuid('assigned_to')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->restrictOnDelete();

            $table
                ->foreign('created_by')
                ->references('id')
                ->on('platform_users')
                ->restrictOnDelete();

            $table
                ->foreign('assigned_to')
                ->references('id')
                ->on('platform_users')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('support_tickets');
    }
};
