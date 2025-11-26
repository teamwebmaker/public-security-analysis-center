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
                'id' => 1,
                'name' => 'GeoTech',
                'economic_activity_type_id' => 1, // e.g., Agriculture
                'identification_code' => '1234567890',
                "economic_activity_code" => '1234567890',
                "high_risk_activities" => true,
                "risk_level" => 'high',
                "evacuation_plan" => true,
                'visibility' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'AgroWorld',
                'economic_activity_type_id' => 2, // e.g., Manufacturing
                'identification_code' => '9876543210',
                "economic_activity_code" => '9876543210',
                "high_risk_activities" => true,
                "risk_level" => 'very high',
                "evacuation_plan" => false,
                'visibility' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
