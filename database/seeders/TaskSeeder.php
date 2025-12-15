<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = \App\Models\Branch::select('id', 'name')->get();
        $services = \App\Models\Service::select('id', 'title')->get();

        if ($branches->isEmpty() || $services->isEmpty()) {
            return;
        }

        $faker = \Faker\Factory::create();
        $now = Carbon::now();
        $tasks = [];

        for ($i = 1; $i <= 5; $i++) {
            $branch = $branches->random();
            $service = $services->random();
            $interval = $faker->numberBetween(5, 30);

            $tasks[] = [
                'branch_id' => $branch->id,
                'branch_name_snapshot' => $branch->name,
                'service_id' => $service->id,
                'service_name_snapshot' => $service->title->ka ?? $service->title->en ?? 'სერვისი',
                'recurrence_interval' => $interval,
                'is_recurring' => true,
                'archived' => '0',
                'visibility' => '1',
                'created_at' => $now->copy()->subDays($faker->numberBetween(0, 10)),
                'updated_at' => $now,
            ];
        }

        DB::table('tasks')->insert($tasks);
    }
}
