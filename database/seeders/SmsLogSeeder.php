<?php

namespace Database\Seeders;

use App\Models\SmsLog;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;

class SmsLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = FakerFactory::create();
        $smsnoOptions = [1, 2];
        $statusOptions = [0, 1, 2,];


        foreach (range(1, 6) as $i) {
            $sentAt = $faker->boolean(70) ? $faker->dateTimeBetween('-10 days', 'now') : null;

            SmsLog::create([
                'provider' => 'sender_ge',
                'provider_message_id' => $faker->boolean(80) ? $faker->uuid : null,
                'destination' => $faker->numerify('5########'),
                'content' => $faker->sentence(4),
                'smsno' => $faker->randomElement($smsnoOptions),
                'status' => $faker->randomElement($statusOptions),
                'provider_response' => [
                    'code' => $faker->randomElement([200, 202, 400]),
                    'message' => $faker->sentence(4),
                ],
                'sent_at' => $sentAt,
            ]);
        }
    }
}
