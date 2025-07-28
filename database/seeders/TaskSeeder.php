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
        DB::table('tasks')->insert([
            [
                'id' => 1,
                'branch_id' => 1,
                'branch_name' => 'GeoTech ფილიალი',
                'service_id' => 1,
                'service_name' => 'საძირკველის ჩაყრა',
                'status_id' => 1,
                'start_date' => now(),
                'end_date' => now(),
                'archived' => '0',
                'visibility' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),

            ],
            [
                'id' => 2,
                'branch_id' => 2,
                'branch_name' => 'AgroWorld ფილიალი',
                'service_id' => 2,
                'service_name' => 'მომხმარებლების პასუხი',
                'status_id' => 1,
                'start_date' => now(),
                'end_date' => now(),
                'archived' => '0',
                'visibility' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
