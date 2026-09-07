<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::connection('landlord')->create('tenant_usage', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('metric', 100);
            $table->bigInteger('value')->unsigned()->default(0);
            $table->timestamp('measured_at');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table
                ->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->restrictOnDelete();

            $table->unique(['tenant_id', 'metric']);
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('tenant_usage');
    }
};
