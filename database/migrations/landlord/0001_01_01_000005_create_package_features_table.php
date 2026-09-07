<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::connection('landlord')->create('package_features', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('package_id');
            $table->string('feature_code', 100);
            $table->boolean('enabled')->default(true);
            $table->json('config')->nullable();
            $table->timestamps();

            $table
                ->foreign('package_id')
                ->references('id')
                ->on('packages')
                ->cascadeOnDelete();

            $table->unique(['package_id', 'feature_code']);
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('package_features');
    }
};
