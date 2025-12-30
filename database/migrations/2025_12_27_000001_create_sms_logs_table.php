<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('sender_ge');
            $table->string('provider_message_id')->nullable();
            $table->string('destination');
            $table->string('content');
            // advertising - 1 or information - 2
            $table->unsignedTinyInteger('smsno');

            // pending - 0, delivered - 1, undelivered - 2
            $table->unsignedTinyInteger('status')->default(0);

            $table->json('provider_response')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index('destination');
            $table->index('provider_message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
