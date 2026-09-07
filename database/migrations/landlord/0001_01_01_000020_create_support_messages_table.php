<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::connection('landlord')->create('support_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ticket_id');
            $table->string('sender_type', 30);
            $table->uuid('sender_id');
            $table->text('message');
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table
                ->foreign('ticket_id')
                ->references('id')
                ->on('support_tickets')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('support_messages');
    }
};
