<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('infos')->insert([
            [
                'title' => json_encode(['ka' => 'ადმინი', 'en' => 'Admin']),
                'description' => json_encode(['ka' => 'PSAC დაფუძნდა 2016 წლის გაზაფხულზე, იმ უსაფრთხოების აქცენტების საპასუხოდ, რომელიც იდგა და დგას ჩვენი საზოგადოების წინაშე. სწორედ ეს აქცენტები დაედო საფუძვლად ჩვენი ქვეყნის მაშტაბით პირველად წარმოგვედგინა სხვადასხვა სახის პროფესიათა უსაფრთხოება როგორც კრიტიკული საკითხი, რომელიც საჭიროებს საფრთხის შემცველ აქცენტებზე ყურადღების გამახვილებას. 9 წელზე მეტია PSAC ჩართულია შრომის უსაფრთხოების ოფიცრის, მძიმე სამშენებლო ტექნიკის მართვის უსაფრთხოების, მობილური ამწის უსაფრთხო ოპერირების, საზოგადოებრივი უსაფრთხოების დაცვის ოფიცრის და უსაფრთხოების სისტემების ტექნიკოსის სწავლებაში, და ამავე დროს პარალელურ რეჟიმში იკვლევს და ქმნის ნოვატორულ უსაფრთხოების აქცენტებით გამოწვეულ პროფესიებს, როგორიცაა საზოგადოებრივი უსაფრთხოების ანალიტიკოსი. PSAC ცდილობს პირველად ჩვენი ქვეყნის რეალობაში გახადოს ხელმისაწვდომი ისეთი უნარ-ჩვევების სწავლება, რომელიც ხელს შეუწყობს ჩვენი ქვეყნის სამოქალაქო საზოგადოებას უსაფრთხოდ განვითარებაში. ჩვენ გადამზადებული გვყავს ათასობით პროფესიონალი, სამოქალაქო პირები, სამხედროები, სამართალდამცავები და უმსხვილესი კომპანიების პერსონალი. ჩვენი ციფრული ინოვაციური სერვისის ბაზაზე, ვეწევით შრომის უსაფრთხოების ოფიცერთა მიმდინარე საქმიანობის მხარდაჭერას და ამავე დროს, ჩვენ ექსპერტებთან ერთად, რომლებიც წარმოადგენენ სხვადასხვა დარგის პროფესიონალებს, ვცდილობთ გამოვიყენოთ მეცნიერება უკეთესი მომავლისათვის და ვითანამშრომლოთ მათთან ვინც თვლის, რომ ცოდნა და რწმენა ერთადერთი გზაა, უსაფრთხო და სტაბილური მომავლის ფორმირებისათვის.', 'en' => 'PSAC was founded in the spring of 2016 in response to the security concerns that have been and continue to be faced by our society. It was these concerns that formed the basis for the first time in our country to present the safety of various professions as a critical issue that requires attention to the risks involved. For more than 9 years, PSAC has been involved in the training of labor safety officers, heavy construction equipment driver safety, mobile crane safe operation, public safety protection officers, and security systems technicians, while at the same time researching and creating innovative professions caused by security concerns, such as a public safety analyst. PSAC is trying to make available, for the first time in the reality of our country, the training of skills that will contribute to the safe development of our civil society. We have trained thousands of professionals, civilians, military personnel, law enforcement officers, and personnel of the largest companies. Based on our digital innovative service, we support the ongoing activities of occupational safety officers and at the same time, together with experts who are professionals from various fields, we strive to use science for a better future and cooperate with those who believe that knowledge and faith are the only way to shape a safe and stable future.']),
                'experience' => 9,
                'graduates' => 2000,
                'image' => 'about-us-image.jpg',
                'phone' => '+995 595 40 11 88',
                'email' => 'psacge@gmail.com',
            ],
        ]);
    }
}
