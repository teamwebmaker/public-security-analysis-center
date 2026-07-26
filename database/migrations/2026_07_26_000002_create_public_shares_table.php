<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('public_shares', function (Blueprint $table) {
            $table->id();
            $table->string('token', 80)->unique();
            $table->morphs('shareable');
            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['shareable_type', 'shareable_id', 'is_active'], 'public_shares_active_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_shares');
    }
};
