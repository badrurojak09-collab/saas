<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::connection('landlord')->create('announcement_recipients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('announcement_id');
            $table->uuid('tenant_id');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table
                ->foreign('announcement_id')
                ->references('id')
                ->on('announcements')
                ->cascadeOnDelete();

            $table
                ->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->restrictOnDelete();

            $table->unique(['announcement_id', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('announcement_recipients');
    }
};
