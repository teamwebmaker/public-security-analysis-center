<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskWorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workers = User::whereHas('role', fn($q) => $q->where('name', 'worker'))->get();
        $tasks = Task::all();

        if ($workers->isEmpty() || $tasks->isEmpty()) {
            return;
        }

        $now = Carbon::now();
        $records = [];

        foreach ($tasks as $task) {
            $selected = $workers->random(min(3, $workers->count()))->all();
            foreach ($selected as $worker) {
                $records[] = [
                    'task_id' => $task->id,
                    'user_id' => $worker->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('task_workers')->insert($records);
    }
}
