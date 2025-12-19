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

   /**
    * Headers for management/worker task list (latest occurrence centric).
    */
   public static function workerTaskHeaders(): array
   {
      return [
         '#',
         'სტატუსი',
         'ფილიალი',
         'სერვისი',
         'კოლეგები',
         'დოკუმენტი',
         'განმეორების თარიღი',
         'დაწყება',
         'დასრულება',
      ];
   }

   /**
    * Headers for company leader task tables (latest occurrence centric).
    */
   public static function companyLeaderTaskHeaders(): array
   {
      return [
         '#',
         'სტატუსი',
         'შემსრულებელი',
         'ფილიალი',
         'სერვისი',
         'დოკუმენტი',
         'დაწყება',
         'დასრულება',
      ];
   }

   /**
    * Headers for responsible person task tables.
    */
   public static function responsiblePersonTaskHeaders(): array
   {
      return [
         '#',
         'სტატუსი',
         'შემსრულებელი',
         'ფილიალი',
         'სერვისი',
         'დოკუმენტი',
         'განმეორების თარიღი',
         'სამუშაო დაიწყო',
         'სამუშაო დასრულდა',
      ];
   }

   /**
    * Headers for responsible person dashboard branches table.
    */
   public static function responsiblePersonBranchHeaders(): array
   {
      return ['#', 'სახელი', 'მისამართი', 'მშობელი კომპანია'];
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
