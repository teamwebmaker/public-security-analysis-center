<?php

namespace App\Presenters;

class TableHeaderDataPresenter
{
   /**
    * Default task headers for admin table.
    */
   public static function taskHeaders(): array
   {
      return ['#', 'ხილვადობა', 'შემსრულებელი', 'ფილიალი', 'სერვისი', "სტატუსი", 'დოკუმენტი', 'განმეორების ინტერვალი', 'სამუშაოს დაწყება', 'სამუშაოს დასრულება', 'სამუშაოს განსაზღვრა'];
   }

   public static function occurrenceHeaders()
   {
      return [
         '#',
         'ფილიალი',
         'სერვისი',
         'შემსრულებლები',
         'ხილვადობა',
         'სტატუსი',
         'გეგმიური თარიღი',
         'დაწყება',
         'დასრულება',
         'დოკუმენტი',
         'ქმედებები',
      ];

   }

}
