<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('student_payments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignUuid('student_bill_id')->constrained('student_bills')->restrictOnDelete();
            $table->string('payment_number', 100)->unique();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method', 50);
            $table->timestamp('paid_at');
            $table->string('reference_number', 100)->nullable();
            $table->string('status', 30)->default('verified')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('student_payments');
    }
};
