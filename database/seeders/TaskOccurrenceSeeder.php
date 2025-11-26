<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskOccurrenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('task_occurrences')->insert([
            [
                'id' => 1,
                'task_id' => 1,
                'branch_id_snapshot' => 1,
                'branch_name_snapshot' => 'GeoTech ფილიალი',
                'service_id_snapshot' => 1,
                'service_name_snapshot' => 'საძირკველის ჩაყრა',
                'scheduled_for' => Carbon::now()->subDays(7)->toDateString(),
                'status_id' => 3, // completed
                'start_date' => Carbon::now()->subDays(7),
                'end_date' => Carbon::now()->subDays(6),
                'requires_document' => true,
                'document_path' => 'report.pdf',
                'payment_status' => 'paid',
                'visibility' => '1',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(6),
            ],
            [
                'id' => 2,
                'task_id' => 1,
                'branch_id_snapshot' => 1,
                'branch_name_snapshot' => 'GeoTech ფილიალი',
                'service_id_snapshot' => 1,
                'service_name_snapshot' => 'საძირკველის ჩაყრა',
                'scheduled_for' => Carbon::now()->toDateString(),
                'status_id' => 1, // pending
                'start_date' => null,
                'end_date' => null,
                'requires_document' => true,
                'document_path' => null,
                'payment_status' => 'unpaid',
                'visibility' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'task_id' => 2,
                'branch_id_snapshot' => 2,
                'branch_name_snapshot' => 'AgroWorld ფილიალი',
                'service_id_snapshot' => 2,
                'service_name_snapshot' => 'მომხმარებლების პასუხი',
                'scheduled_for' => Carbon::now()->addDays(1)->toDateString(),
                'status_id' => 2, // in_progress
                'start_date' => Carbon::now()->addHours(1),
                'end_date' => null,
                'requires_document' => false,
                'document_path' => null,
                'payment_status' => 'pending',
                'visibility' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
