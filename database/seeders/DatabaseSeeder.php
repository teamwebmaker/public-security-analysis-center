<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            EconomicActivityType::class,
            CompanySeeder::class,
            CompanyLeadersSeeder::class,
            BranchSeeder::class,
            TaskStatusSeeder::class,
            TaskSeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class
        ]);
    }
}
