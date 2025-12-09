<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TaskOccurrenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // $faker = Faker::create();

        // $records = [
        //     [
        //         'id' => 1,
        //         'task_id' => 1,
        //         'branch_id_snapshot' => 1,
        //         'branch_name_snapshot' => 'GeoTech ფილიალი',
        //         'service_id_snapshot' => 1,
        //         'service_name_snapshot' => 'საძირკველის ჩაყრა',
        //         'due_date' => Carbon::now()->subDays(7)->toDateString(),
        //         'status_id' => 3,
        //         'start_date' => Carbon::now()->subDays(7),
        //         'end_date' => Carbon::now()->subDays(6),
        //         'requires_document' => true,
        //         'document_path' => 'report.pdf',
        //         'payment_status' => 'paid',
        //         'visibility' => '1',
        //         'created_at' => Carbon::now()->subDays(7),
        //         'updated_at' => Carbon::now()->subDays(6),
        //     ],
        //     [
        //         'id' => 2,
        //         'task_id' => 1,
        //         'branch_id_snapshot' => 1,
        //         'branch_name_snapshot' => 'GeoTech ფილიალი',
        //         'service_id_snapshot' => 1,
        //         'service_name_snapshot' => 'საძირკველის ჩაყრა',
        //         'due_date' => Carbon::now()->toDateString(),
        //         'status_id' => 1,
        //         'start_date' => null,
        //         'end_date' => null,
        //         'requires_document' => true,
        //         'document_path' => null,
        //         'payment_status' => 'unpaid',
        //         'visibility' => '1',
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //     ],
        //     [
        //         'id' => 3,
        //         'task_id' => 2,
        //         'branch_id_snapshot' => 2,
        //         'branch_name_snapshot' => 'AgroWorld ფილიალი',
        //         'service_id_snapshot' => 2,
        //         'service_name_snapshot' => 'მომხმარებლების პასუხი',
        //         'due_date' => Carbon::now()->addDays(1)->toDateString(),
        //         'status_id' => 2,
        //         'start_date' => Carbon::now()->addHours(1),
        //         'end_date' => null,
        //         'requires_document' => false,
        //         'document_path' => null,
        //         'payment_status' => 'pending',
        //         'visibility' => '1',
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //     ],
        // ];

        // // ---------------------------------------------
        // // Generate 10 more fake occurrences (IDs 4–13)
        // // ---------------------------------------------

        // for ($id = 4; $id <= 23; $id++) {

        //     $startDate = $faker->optional()->dateTimeBetween('-10 days', 'now');
        //     $endDate = $startDate ? $faker->optional()->dateTimeBetween($startDate, 'now') : null;

        //     $records[] = [
        //         'id' => $id,
        //         'task_id' => 1,
        //         'branch_id_snapshot' => 1,
        //         'branch_name_snapshot' => 'GeoTech ფილიალი',
        //         'service_id_snapshot' => 1,
        //         'service_name_snapshot' => 'საძირკველის ჩაყრა',

        //         'due_date' => $faker->dateTimeBetween('-7 days', '+10 days')->format('Y-m-d'),
        //         'status_id' => $faker->randomElement([1, 2, 3]),

        //         'start_date' => $startDate,
        //         'end_date' => $endDate,

        //         'requires_document' => $faker->boolean(),
        //         'document_path' => $faker->boolean() ? $faker->randomElement([
        //             'docs/file1.pdf',
        //             'docs/file2.pdf',
        //             'docs/reportA.pdf',
        //             null
        //         ]) : null,

        //         'payment_status' => $faker->randomElement(['paid', 'unpaid', 'pending']),
        //         'visibility' => '1',

        //         'created_at' => Carbon::now()->subDays(rand(1, 10)),
        //         'updated_at' => Carbon::now()->subDays(rand(0, 5)),
        //     ];
        // }

        // // Insert all records
        // DB::table('task_occurrences')->insert($records);



        DB::table('task_occurrences')->insert([
            [
                'id' => 1,
                'task_id' => 1,
                'branch_id_snapshot' => 1,
                'branch_name_snapshot' => 'GeoTech ფილიალი',
                'service_id_snapshot' => 1,
                'service_name_snapshot' => 'საძირკველის ჩაყრა',
                'due_date' => Carbon::now()->subDays(7)->toDateString(),
                'status_id' => 3, // completed
                'start_date' => Carbon::now()->subDays(7),
                'end_date' => Carbon::now()->subDays(6),
                'requires_document' => true,
                'document_path' => 'report.pdf',
                'payment_status' => 'paid',
                'visibility' => '1',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(6),
            ],
            [
                'id' => 2,
                'task_id' => 1,
                'branch_id_snapshot' => 1,
                'branch_name_snapshot' => 'GeoTech ფილიალი',
                'service_id_snapshot' => 1,
                'service_name_snapshot' => 'საძირკველის ჩაყრა',
                'due_date' => Carbon::now()->toDateString(),
                'status_id' => 1, // pending
                'start_date' => null,
                'end_date' => null,
                'requires_document' => true,
                'document_path' => null,
                'payment_status' => 'unpaid',
                'visibility' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'task_id' => 2,
                'branch_id_snapshot' => 2,
                'branch_name_snapshot' => 'AgroWorld ფილიალი',
                'service_id_snapshot' => 2,
                'service_name_snapshot' => 'მომხმარებლების პასუხი',
                'due_date' => Carbon::now()->addDays(1)->toDateString(),
                'status_id' => 2, // in_progress
                'start_date' => Carbon::now()->addHours(1),
                'end_date' => null,
                'requires_document' => false,
                'document_path' => null,
                'payment_status' => 'pending',
                'visibility' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
