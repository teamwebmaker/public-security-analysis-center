<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('task_occurrences', function (Blueprint $table) {
            $table->string('payment_proof_path')->nullable()->after('payment_status');
            $table->string('payment_proof_original_name')->nullable()->after('payment_proof_path');
            $table->string('payment_proof_mime_type')->nullable()->after('payment_proof_original_name');
            $table->foreignId('payment_proof_uploaded_by_user_id')
                ->nullable()
                ->after('payment_proof_mime_type')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('payment_proof_uploaded_at')->nullable()->after('payment_proof_uploaded_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('task_occurrences', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_proof_uploaded_by_user_id');
            $table->dropColumn([
                'payment_proof_path',
                'payment_proof_original_name',
                'payment_proof_mime_type',
                'payment_proof_uploaded_at',
            ]);
        });
    }
};
