<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
   public function run(): void
   {
      DB::table('roles')->insert([

         [
            'id' => 1,
            'name' => 'admin',
            'display_name' => 'ადმინი'
         ],
         [
            'id' => 2,
            'name' => 'company_admin',
            'display_name' => 'კომპანიის ადმინი'
         ],
         [
            'id' => 3,
            'name' => 'responsible_person',
            'display_name' => 'პასუხისმგებელი პირი'
         ],
         [
            'id' => 4,
            'name' => 'worker',
            'display_name' => 'შემსრულებელი'
         ],


      ]);
   }
}
