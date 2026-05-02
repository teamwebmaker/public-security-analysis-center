<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE mentors ADD COLUMN full_name_translations JSON NULL AFTER full_name');
            DB::statement("UPDATE mentors SET full_name_translations = CASE WHEN JSON_VALID(full_name) = 1 THEN full_name ELSE JSON_OBJECT('ka', full_name, 'en', full_name) END");
            DB::statement('ALTER TABLE mentors DROP COLUMN full_name');
            DB::statement('ALTER TABLE mentors CHANGE full_name_translations full_name JSON NOT NULL');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE mentors ADD COLUMN full_name_translations json');
            DB::statement("UPDATE mentors SET full_name_translations = json_build_object('ka', full_name, 'en', full_name)");
            DB::statement('ALTER TABLE mentors DROP COLUMN full_name');
            DB::statement('ALTER TABLE mentors RENAME COLUMN full_name_translations TO full_name');
            DB::statement('ALTER TABLE mentors ALTER COLUMN full_name SET NOT NULL');
            return;
        }

        if ($driver === 'sqlite') {
            DB::statement("UPDATE mentors SET full_name = json_object('ka', full_name, 'en', full_name) WHERE json_valid(full_name) = 0");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE mentors ADD COLUMN full_name_string VARCHAR(255) NULL AFTER full_name');
            DB::statement("UPDATE mentors SET full_name_string = COALESCE(JSON_UNQUOTE(JSON_EXTRACT(full_name, '$.ka')), JSON_UNQUOTE(JSON_EXTRACT(full_name, '$.en')), '')");
            DB::statement('ALTER TABLE mentors DROP COLUMN full_name');
            DB::statement('ALTER TABLE mentors CHANGE full_name_string full_name VARCHAR(255) NOT NULL');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE mentors ADD COLUMN full_name_string varchar(255)');
            DB::statement("UPDATE mentors SET full_name_string = COALESCE(full_name->>'ka', full_name->>'en', '')");
            DB::statement('ALTER TABLE mentors DROP COLUMN full_name');
            DB::statement('ALTER TABLE mentors RENAME COLUMN full_name_string TO full_name');
            DB::statement('ALTER TABLE mentors ALTER COLUMN full_name SET NOT NULL');
            return;
        }

        if ($driver === 'sqlite') {
            DB::statement("UPDATE mentors SET full_name = json_extract(full_name, '$.ka') WHERE json_valid(full_name) = 1");
        }
    }
};
