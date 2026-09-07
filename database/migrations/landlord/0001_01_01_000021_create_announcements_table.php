<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::connection('landlord')->create('announcements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 255);
            $table->longText('content');
            $table->string('type', 30);
            $table->string('status', 30);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->uuid('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign('created_by')
                ->references('id')
                ->on('platform_users')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('announcements');
    }
};
