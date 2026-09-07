<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('student_profiles', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->string('student_number', 50)->unique();
            $table->string('full_name', 200);
            $table->string('status', 30)->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index('status');
        });

        Schema::connection('tenant')->create('lecturer_profiles', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->string('employee_number', 50)->nullable()->unique();
            $table->string('full_name', 200);
            $table->string('status', 30)->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index('status');
        });

        Schema::connection('tenant')->create('staff_profiles', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->string('employee_number', 50)->nullable()->unique();
            $table->string('full_name', 200);
            $table->string('status', 30)->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index('status');
        });

        Schema::connection('tenant')->create('organization_memberships', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('organization_type', 50);
            $table->uuid('organization_id');
            $table->string('membership_type', 50)->default('member');
            $table->boolean('is_primary')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'organization_type', 'organization_id'], 'org_memberships_user_org_unique');
            $table->index(['organization_type', 'organization_id']);
            $table->index(['user_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('organization_memberships');
        Schema::connection('tenant')->dropIfExists('staff_profiles');
        Schema::connection('tenant')->dropIfExists('lecturer_profiles');
        Schema::connection('tenant')->dropIfExists('student_profiles');
    }
};
