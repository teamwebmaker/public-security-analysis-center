<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('incident_user_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();

            $table->unique(['incident_id', 'user_id']);
            $table->index(['user_id', 'signed_at']);
        });

        Schema::create('incident_external_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('phone', 30)->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->foreignId('signed_marked_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['incident_id', 'signed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_external_participants');
        Schema::dropIfExists('incident_user_participants');
    }
};
