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
                // Users
            RoleSeeder::class,
            UserSeeder::class,
                // Companies
            EconomicActivityType::class,
            CompanySeeder::class,
            CompanyLeadersSeeder::class,
                // Services
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
                // Branches
            BranchSeeder::class,
            ResponsiblePersonBranchSeeder::class,
            ResponsiblePersonServiceSeeder::class,
                // Tasks
            TaskStatusSeeder::class,
            TaskSeeder::class,
            TaskWorkerSeeder::class
        ]);
    }
}
