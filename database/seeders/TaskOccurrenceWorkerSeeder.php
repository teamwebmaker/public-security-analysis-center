<?php

namespace Database\Seeders;

use App\Models\TaskOccurrence;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskOccurrenceWorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workers = User::whereHas('role', fn($q) => $q->where('name', 'worker'))->get();
        $occurrences = TaskOccurrence::all();

        if ($workers->isEmpty() || $occurrences->isEmpty()) {
            return;
        }

        $records = [];

        foreach ($occurrences as $occurrence) {
            $selected = $workers->random(min(3, $workers->count()))->all();

            foreach ($selected as $worker) {
                $records[] = [
                    'task_occurrence_id' => $occurrence->id,
                    'worker_id_snapshot' => $worker->id,
                    'worker_name_snapshot' => $worker->full_name,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        DB::table('task_occurrence_workers')->insert($records);
    }
}
