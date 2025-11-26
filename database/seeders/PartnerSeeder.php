<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('partners')->insert([
            [
                'id' => 10,
                'title' => 'AB Security',
                'image' => 'partner-slide-03.jpg',
                'link' => 'https://absecurity.ge/',
                'visibility' => '1',
                'created_at' => '2025-05-27 02:17:21',
                'updated_at' => '2025-05-27 02:17:21',
            ],
            [
                'id' => 11,
                'title' => 'MOH',
                'image' => 'partner-slide-01.jpg',
                'link' => 'https://www.moh.gov.ge/',
                'visibility' => '1',
                'created_at' => '2025-05-27 02:17:21',
                'updated_at' => '2025-07-02 05:37:42',
            ],
            [
                'id' => 12,
                'title' => 'PROFGLDANI',
                'image' => 'partner-slide-08.jpg',
                'link' => 'https://www.profgldani.ge/',
                'visibility' => '1',
                'created_at' => '2025-05-27 02:17:21',
                'updated_at' => '2025-05-27 02:17:21',
            ],
            [
                'id' => 13,
                'title' => 'Victoria Security',
                'image' => 'partner-slide-04.jpg',
                'link' => 'https://www.victoriasecurity.ge/',
                'visibility' => '1',
                'created_at' => '2025-05-27 02:17:21',
                'updated_at' => '2025-05-27 02:17:21',
            ],
            [
                'id' => 14,
                'title' => 'GTU',
                'image' => 'partner-slide-05.jpg',
                'link' => 'https://gtu.ge/',
                'visibility' => '1',
                'created_at' => '2025-05-27 02:17:21',
                'updated_at' => '2025-05-27 02:17:21',
            ],
            [
                'id' => 15,
                'title' => 'EQE',
                'image' => 'partner-slide-07.jpg',
                'link' => 'https://eqe.ge/en',
                'visibility' => '1',
                'created_at' => '2025-05-27 02:17:21',
                'updated_at' => '2025-05-27 02:17:21',
            ],
            [
                'id' => 16,
                'title' => 'Black Sea Group',
                'image' => '684e9712d016a-1749980946.jpg',
                'link' => 'https://bsg.com.ge/',
                'visibility' => '1',
                'created_at' => '2025-05-27 02:17:21',
                'updated_at' => '2025-07-01 21:12:40',
            ],
        ]);
    }
}
