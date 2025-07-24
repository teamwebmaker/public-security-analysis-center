<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            [
                'name' => 'GeoTech Solutions Ltd.',
                'economic_activity_type_id' => 1, // e.g., Agriculture
                'identification_code' => '1234567890',
                'visibility' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AgroWorld Georgia',
                'economic_activity_type_id' => 2, // e.g., Manufacturing
                'identification_code' => '9876543210',
                'visibility' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
