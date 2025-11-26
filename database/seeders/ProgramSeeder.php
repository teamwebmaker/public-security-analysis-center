<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('programs')->insert([
            [
                'id' => 1,
                'title' => json_encode([
                    'ka' => 'ლიდერობის პროგრამა',
                    'en' => 'Leadership Program',
                ]),
                'description' => json_encode([
                    'ka' => 'სამხედროების, სამართალდამცავი ორგანოების, დიპლომატებისა და სტუდენტების მომზადების 15-წლიანი გამოცდილებით, CASE წარმოგიდგენთ დაზვერვისა და ეროვნული უსაფრთხოების ინტენსიურ სასწავლო კურსს SSNS25. ზაფხულის სკოლა ყოველწლიურად ტარდება და ათასობით სერტიფიცირებულ პროფესიონალს ითვლის. CASE საუკეთესო ადგილია კარიერული ზრდისა და პროფესიული ქსელური ურთიერთობებისთვის. ეს უნიკალური შესაძლებლობაა უსაფრთხოების სპეციალისტებისა და დაინტერესებული მხარეებისთვის, მიიღონ პირველადი ცოდნა ისეთ თემებზე, რომლებიც არასდროს ყოფილა ასეთი ხელმისაწვდომი, შეიძინონ პროფესიული უნარები და მიიღონ აღიარებული სერტიფიკატი.',
                    'en' => 'With 15 years of experience training military, law enforcement, diplomats and students, CASE presents the intensive training course in Intelligence and National Security SSNS25. Summer school is held every year and counts thousands of certified professionals. CASE is the best place for career growth and professional networking. This is a unique opportunity for security professionals and interested parties to gain first-hand insider knowledge on topics that have never been more accessible, gain professional skills and gain recognized certification.',
                ]),
                'image' => 'img_1.png',
                'certificate_image' => 'certificate_img-1.png',
                'video' => 'https://www.youtube.com/embed/dQw4w9WgXcQ?si=ztlw9Yv2mhVYtQ2M',
                'price' => '500',
                'start_date' => '2025-07-01',
                'end_date' => '2025-08-01',
                'days' => json_encode([
                    'ka' => ['ორშაბათი', 'სამშაბათი', 'ოთხშაბათი'],
                    'en' => ['Monday', 'Tuesday', 'Wednesday'],
                ]),
                'hour' => json_encode([
                    'start' => '10:00',
                    'end' => '15:00',
                ]),
                'duration' => '1 თვე',
                'address' => 'საქართველო, თბილისი',
                'visibility' => '1',
                'sortable' => 1,
                'created_at' => '2025-05-26 21:44:40',
                'updated_at' => '2025-07-01 00:37:00',
            ],
        ]);
    }
}
