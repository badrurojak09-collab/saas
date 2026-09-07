<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::connection('landlord')->create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('invoice_id');
            $table->decimal('amount', 18, 2)->default(0);
            $table->char('currency', 3)->default('IDR');
            $table->string('method', 50);
            $table->string('status', 30)->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('reference', 150)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table
                ->foreign('invoice_id')
                ->references('id')
                ->on('invoices')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('payments');
    }
};
