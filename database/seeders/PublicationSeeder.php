<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PublicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('publications')->insert([
            [
                'id' => 1,
                'title' => json_encode([
                    'ka' => 'პუბლიკაცია 1',
                    'en' => 'Publication 1',
                ]),
                'description' => json_encode([
                    'ka' => 'სამხედროების, სამართალდამცავი ორგანოების, დიპლომატებისა და სტუდენტების მომზადების 15-წლიანი გამოცდილებით, CASE წარმოგიდგენთ დაზვერვისა და ეროვნული უსაფრთხოების ინტენსიურ სასწავლო კურსს SSNS25. ზაფხულის სკოლა ყოველწლიურად ტარდება და ათასობით სერტიფიცირებულ პროფესიონალს ითვლის. CASE საუკეთესო ადგილია კარიერული ზრდისა და პროფესიული ქსელური ურთიერთობებისთვის. ეს უნიკალური შესაძლებლობაა უსაფრთხოების სპეციალისტებისა და დაინტერესებული მხარეებისთვის, მიიღონ პირველადი ცოდნა ისეთ თემებზე, რომლებიც არასდროს ყოფილა ასეთი ხელმისაწვდომი, შეიძინონ პროფესიული უნარები და მიიღონ აღიარებული სერტიფიკატი.',
                    'en' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
                ]),
                'image' => 'img_1.jpg',
                'file' => 'publication-pdf-1.pdf',
                'visibility' => '1',
                'created_at' => '2025-05-27 01:44:40',
                'updated_at' => '2025-07-01 00:37:11',
            ],
        ]);
    }
}
