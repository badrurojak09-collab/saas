<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::connection('landlord')->create('tenant_addons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('addon_id');
            $table->string('status', 30)->default('active');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->decimal('price', 18, 2)->default(0);
            $table->char('currency', 3)->default('IDR');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table
                ->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->restrictOnDelete();

            $table
                ->foreign('addon_id')
                ->references('id')
                ->on('addons')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('tenant_addons');
    }
};
