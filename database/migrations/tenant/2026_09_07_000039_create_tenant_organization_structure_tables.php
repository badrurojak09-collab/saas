<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::connection('tenant')->create('perguruan_tinggi', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->string('short_name', 75)->nullable();
            $table->string('legal_name', 200)->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->text('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::connection('tenant')->create('organization_units', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->uuid('perguruan_tinggi_id');
            $table->string('code', 50);
            $table->string('name', 150);
            $table->string('short_name', 75)->nullable();
            $table->string('type', 30);
            $table->string('status', 30)->default('draft');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('parent_id')->references('id')->on('organization_units')->restrictOnDelete();
            $table->foreign('perguruan_tinggi_id')->references('id')->on('perguruan_tinggi')->restrictOnDelete();
            $table->unique(['perguruan_tinggi_id', 'code']);
            $table->index(['parent_id', 'perguruan_tinggi_id']);
            $table->index('type');
            $table->index('status');
        });

        Schema::connection('tenant')->table('organization_memberships', function (Blueprint $table): void {
            $table->uuid('organization_unit_id')->nullable()->after('user_id');
            $table->softDeletes();
            $table->foreign('organization_unit_id')->references('id')->on('organization_units')->restrictOnDelete();
            $table->index('organization_unit_id');
            $table->dropUnique('org_memberships_user_org_unique');
            $table->dropIndex(['organization_type', 'organization_id']);
            $table->dropColumn(['organization_type', 'organization_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->table('organization_memberships', function (Blueprint $table): void {
            $table->dropForeign(['organization_unit_id']);
            $table->dropIndex(['organization_unit_id']);
            $table->dropColumn('organization_unit_id');
            $table->dropSoftDeletes();
        });

        Schema::connection('tenant')->dropIfExists('organization_units');
        Schema::connection('tenant')->dropIfExists('perguruan_tinggi');
    }
};
