<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskOccurrenceWorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('task_occurrence_workers')->insert([
            [
                'task_occurrence_id' => 1,
                'worker_id_snapshot' => 5,
                'worker_name_snapshot' => 'მუშა-1',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(6),
            ],
            [
                'task_occurrence_id' => 1,
                'worker_id_snapshot' => 6,
                'worker_name_snapshot' => 'მუშა-2',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(6),
            ],
            [
                'task_occurrence_id' => 2,
                'worker_id_snapshot' => 5,
                'worker_name_snapshot' => 'მუშა-1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'task_occurrence_id' => 3,
                'worker_id_snapshot' => 6,
                'worker_name_snapshot' => 'მუშა-2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
