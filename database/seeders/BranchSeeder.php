<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('branches')->insert([
            [
                'name' => 'GeoTech ფილიალი',
                'address' => 'Georgia,Tbilisi',
                'company_id' => 1,
                'visibility' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AgroWorld ფილიალი',
                'address' => 'Georgia,Tbilisi',
                'company_id' => 2,
                'visibility' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // [
            //     'name' => 'color ფილიალი',
            //     'address' => 'Georgia,Tbilisi',
            //     'company_id' => 1,
            //     'visibility' => '1',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'name' => 'table ფილიალი',
            //     'address' => 'Georgia,Tbilisi',
            //     'company_id' => 2,
            //     'visibility' => '1',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]
        ]);
    }
}
