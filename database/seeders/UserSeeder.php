<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([

            [
                'full_name' => 'main admin',
                'email' => 'psacge@gmail.com',
                'phone' => '1234567890',
                'password' => bcrypt('psacge@gmail.compsacge@gmail.com'),
                'role_id' => 1,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'full_name' => 'კომპანიის ადმინი',
                'email' => null,
                'phone' => '111111111',
                'password' => bcrypt('111111111'),
                'role_id' => 2, // company_leader
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'full_name' => 'პასუხისმგებელი პირი-1',
                'email' => null,
                'phone' => '222222222',
                'password' => bcrypt('222222222'),
                'role_id' => 3, // responsible_person
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'full_name' => 'პასუხისმგებელი პირი-2',
                'email' => null,
                'phone' => '2222222222',
                'password' => bcrypt('2222222222'),
                'role_id' => 3, // responsible_person
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'full_name' => 'მუშა-1',
                'email' => null,
                'phone' => '333333333',
                'password' => bcrypt('333333333'),
                'role_id' => 4, // worker
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'full_name' => 'მუშა-2',
                'email' => null,
                'phone' => '3333333332',
                'password' => bcrypt('3333333332'),
                'role_id' => 4, // worker
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
