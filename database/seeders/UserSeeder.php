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
                'email' => 'test@admin.com',
                'phone' => '1234567890',
                'password' => bcrypt('test@admin.com'),
                'role_id' => 1,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'full_name' => 'user1',
                'email' => null,
                'phone' => '111111111',
                'password' => bcrypt('111111111'),
                'role_id' => 2,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'full_name' => 'user2',
                'email' => null,
                'phone' => '222222222',
                'password' => bcrypt('222222222'),
                'role_id' => 3,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'full_name' => 'user3',
                'email' => null,
                'phone' => '333333333',
                'password' => bcrypt('333333333'),
                'role_id' => 4,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
