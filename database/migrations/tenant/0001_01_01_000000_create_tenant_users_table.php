<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('users', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name', 150);
            $table->string('email', 255)->nullable()->unique();
            $table->string('username', 100)->nullable()->unique();
            $table->string('password');
            $table->string('phone', 30)->nullable();
            $table->string('avatar_path', 500)->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->string('user_type', 30)->default('admin')->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->json('metadata')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('users');
    }
};
