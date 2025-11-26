<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('projects')->insert([
            [
                'id' => 2,
                'title' => json_encode([
                    'ka' => 'ინოვაციური პლატფორმა',
                    'en' => 'Innovative Platform',
                ]),
                'description' => json_encode([
                    'ka' => 'პლატფორმა ციფრული პროდუქტებისთვის',
                    'en' => 'A platform for digital products',
                ]),
                'image' => '659ea7567ca60-1704896342.png',
                'visibility' => '1',
                'created_at' => '2025-06-27 02:11:56',
                'updated_at' => '2025-07-01 06:50:32',
            ],
            [
                'id' => 3,
                'title' => json_encode([
                    'ka' => 'გლობალური სწავლა',
                    'en' => 'Global Learning',
                ]),
                'description' => json_encode([
                    'ka' => 'გლობალური სასწავლო პროგრამაგლობალური სასწავლო პროგრამაგლობალური სასწავლო პროგრამაგლობალური სასწავლო პროგრამაგლობალური სასწავლო პროგრამაგლობალური სასწავლო პროგრამა',
                    'en' => 'A global educational initiative',
                ]),
                'image' => '659eabd360801-1704897491.png',
                'visibility' => '1',
                'created_at' => '2025-03-27 02:11:56',
                'updated_at' => '2025-10-28 07:56:40',
            ],
            [
                'id' => 4,
                'title' => json_encode([
                    'ka' => 'ეკო ინიციატივა',
                    'en' => 'Eco Initiative',
                ]),
                'description' => json_encode([
                    'ka' => 'ეკოლოგიური პროექტი მომავლისთვის',
                    'en' => 'An ecological project for the future',
                ]),
                'image' => 'img_1.png',
                'visibility' => '1',
                'created_at' => '2025-01-27 02:11:56',
                'updated_at' => '2025-07-01 06:50:26',
            ],
        ]);
    }
}
