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
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->enum('event_type', [
                'task_assigned',
                'task_started',
                'task_finished',
                'debt_due_2_days',
                'debt_overdue_service_suspended',
            ])->nullable()->after('content');

            $table->unsignedBigInteger('entity_id')->nullable()->after('event_type');

            $table->enum('recipient_type', [
                'worker',
                'responsible_person',
                'company_leader',
                'admin',
            ])->nullable()->after('entity_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->dropColumn(['event_type', 'entity_id', 'recipient_type']);
        });
    }
};
