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
            TaskSeeder::class,
                // Task Occurrences
            TaskOccurrenceStatusSeeder::class,
            TaskWorkerSeeder::class,
            TaskOccurrenceSeeder::class,
            TaskOccurrenceWorkerSeeder::class,

            InfoSeeder::class,
            PartnerSeeder::class,
            PublicationSeeder::class,
            ProgramSeeder::class,
            ProjectSeeder::class,

                // Sms
            SmsLogSeeder::class

        ]);
    }
}
