<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('services')->insert([
            [
                'id' => 1,
                'title' => json_encode(['ka' => 'საძირკველის ჩაყრა', 'en' => 'Laying the foundation']),
                'description' => json_encode(['ka' => 'დამატებითი მომსახურების განათლება', 'en' => 'Additional service education']),
                'service_category_id' => 1,
                'image' => '/public/images/services/service_img-2.jpg',
                'visibility' => '1',
                'sortable' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'title' => json_encode(['ka' => 'მომხმარებლების პასუხი', 'en' => 'customer service']),
                'description' => json_encode(['ka' => 'დამატებითი მომსახურების განათლებაგანათლებაგანათლება', 'en' => 'Additional service educationeducationeducation']),
                'service_category_id' => 2,
                'image' => '/public/images/services/service_img-3.jpg',
                'visibility' => '1',
                'sortable' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
