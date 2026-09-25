<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (! Schema::hasColumn('messages', 'action_url')) {
                $table->string('action_url', 2048)->nullable()->after('message');
            }

            if (! Schema::hasColumn('messages', 'action_label')) {
                $table->string('action_label')->nullable()->after('action_url');
            }

            if (! Schema::hasColumn('messages', 'context')) {
                $table->json('context')->nullable()->after('action_label');
            }
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('messages', 'context') ? 'context' : null,
                Schema::hasColumn('messages', 'action_label') ? 'action_label' : null,
                Schema::hasColumn('messages', 'action_url') ? 'action_url' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
