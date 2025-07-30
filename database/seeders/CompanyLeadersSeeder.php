<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyLeadersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('company_leaders')->insert([
            [
                'user_id' => 2,
                'company_id' => 1
            ],
            [
                'user_id' => 2,
                'company_id' => 2
            ]

        ]);
    }
}
