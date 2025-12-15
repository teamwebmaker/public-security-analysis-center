<?php

namespace Database\Seeders;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TaskOccurrenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $tasks = Task::all();

        if ($tasks->isEmpty()) {
            return;
        }

        $statuses = [1, 2, 3, 4, 5]; // IDs from TaskOccurrenceStatusSeeder
        $records = [];

        foreach ($tasks as $task) {
            $baseDate = $task->created_at ?? Carbon::now();
            $interval = $task->recurrence_interval ?: $faker->numberBetween(5, 30);

            for ($i = 0; $i < 5; $i++) {
                $statusId = $faker->randomElement($statuses);
                $dueDate = $baseDate->copy()->addDays(($i + 1) * $interval);

                $startDate = null;
                $endDate = null;

                if (in_array($statusId, [2, 3, 4])) { // in_progress, completed, on_hold
                    $startDate = $dueDate->copy()->subDays($faker->numberBetween(0, 2))->setTime(rand(8, 12), rand(0, 59));
                }

                if ($statusId === 3) { // completed
                    $endDate = $startDate?->copy()->addHours($faker->numberBetween(2, 8));
                }

                $records[] = [
                    'task_id' => $task->id,
                    'branch_id_snapshot' => $task->branch_id,
                    'branch_name_snapshot' => $task->branch_name_snapshot,
                    'service_id_snapshot' => $task->service_id,
                    'service_name_snapshot' => $task->service_name_snapshot,
                    'due_date' => $dueDate->toDateString(),
                    'status_id' => $statusId,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'requires_document' => $faker->boolean(),
                    'document_path' => null,
                    'payment_status' => $faker->randomElement(['paid', 'unpaid', 'pending']),
                    'visibility' => '1',
                    'created_at' => $dueDate->copy()->subDays(1),
                    'updated_at' => $dueDate->copy()->subHours(1),
                ];
            }
        }

        DB::table('task_occurrences')->insert($records);
    }
}
