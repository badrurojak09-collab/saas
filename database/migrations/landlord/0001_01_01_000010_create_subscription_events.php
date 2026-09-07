<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::connection('landlord')->create('subscription_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_subscription_id');
            $table->string('event_type', 50);
            $table->string('old_status', 30)->nullable();
            $table->string('new_status', 30)->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table
                ->foreign('tenant_subscription_id')
                ->references('id')
                ->on('tenant_subscriptions')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('subscription_events');
    }
};
