<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('task_worker_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inviter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('invited_worker_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['task_id', 'invited_worker_id'], 'task_worker_invitation_unique');
            $table->index(['invited_worker_id', 'status']);
            $table->index(['inviter_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_worker_invitations');
    }
};
