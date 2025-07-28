<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('service_categories')->insert([
            [
                'id' => 1,
                'name' => json_encode(['ka' => 'მშენებლობა', 'en' => 'construction']),
                'visibility' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'name' => json_encode(['ka' => 'საკონტაქტო სერვისი', 'en' => 'Contact service']),
                'visibility' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),

            ]
        ]);
    }
}
