<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE sms_logs MODIFY event_type ENUM(
            'task_assigned',
            'task_started',
            'task_finished',
            'debt_due_2_days',
            'debt_overdue_service_suspended',
            'incident_created',
            'incident_shared'
        ) NULL");

        DB::statement("ALTER TABLE sms_logs MODIFY recipient_type ENUM(
            'worker',
            'responsible_person',
            'company_leader',
            'admin',
            'external_person'
        ) NULL");
    }

    public function down(): void
    {
        // Existing incident logs must remain valid, so enum values are intentionally retained.
    }
};
