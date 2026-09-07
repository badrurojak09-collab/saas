<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::connection('tenant')->create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->uuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::connection('tenant')->create('user_invitations', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('email');
            $table->string('token_hash', 64)->unique();
            $table->json('roles')->nullable();
            $table->uuid('invited_by')->nullable();
            $table->string('status', 30)->default('pending');
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
            $table->index(['email', 'status']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('user_invitations');
        Schema::connection('tenant')->dropIfExists('sessions');
        Schema::connection('tenant')->dropIfExists('password_reset_tokens');
    }
};
