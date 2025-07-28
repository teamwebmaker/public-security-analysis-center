<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('task_statuses')->insert([
            [
                'id' => 1,
                'name' => 'pending',
                'display_name' => 'მოლოდინში',
            ],
            [
                'id' => 2,
                'name' => 'in_progress',
                'display_name' => 'პროცესშია',
            ],
            [
                'id' => 3,
                'name' => 'completed',
                'display_name' => 'დასრულებული',
            ],
            [
                'id' => 4,
                'name' => 'on_hold',
                'display_name' => 'შეჩერებული',
            ],
            [
                'id' => 5,
                'name' => 'cancelled',
                'display_name' => 'გაუქმებული',
            ],
        ]);
    }
}
