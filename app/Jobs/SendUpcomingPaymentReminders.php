<?php

namespace App\Jobs;

use App\Models\TaskOccurrence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendUpcomingPaymentReminders implements ShouldQueue
{
   use Dispatchable;
   use InteractsWithQueue;
   use Queueable;
   use SerializesModels;

   /**
    * Log reminders two days before due date for unpaid occurrences.
    */
   public function handle(): void
   {
      $reminderDate = now('Asia/Tbilisi')->addDays(2)->toDateString();

      TaskOccurrence::query()
         ->whereDate('due_date', '=', $reminderDate)
         ->where('payment_status', 'unpaid')
         ->with(['task.branch.users'])
         ->chunkById(100, function ($occurrences) {
            foreach ($occurrences as $occurrence) {
               $this->logForResponsiblePersons(
                  $occurrence,
                  'Upcoming payment reminder',
                  'Payment is due in 2 days. Service will be stopped if payment is not completed.'
               );
            }
         });
   }

   /**
    * Log notifications for responsible persons on a branch with idempotent guard.
    */
   protected function logForResponsiblePersons(TaskOccurrence $occurrence, string $type, string $message): void
   {
      $users = $occurrence->task?->branch?->users ?? collect();

      if ($users->isEmpty()) {
         Log::warning('Payment notification skipped: no responsible persons found.', [
            'type' => $type,
            'occurrence_id' => $occurrence->id,
            'due_date' => $occurrence->due_date?->toDateString(),
         ]);
         return;
      }

      foreach ($users as $user) {
         $key = sprintf(
            'payment_notice:%s:%s:%s',
            $type,
            $occurrence->id,
            $user->id
         );
         if (!Cache::add($key, true, now()->addDays(3))) {
            continue;
         }

         Log::info('Payment notification', [
            'type' => $type,
            'responsible_person' => $user->full_name,
            'phone' => $user->phone,
            'occurrence_id' => $occurrence->id,
            'task_id' => $occurrence->task_id,
            'due_date' => $occurrence->due_date?->toDateString(),
            'message' => $message,
         ]);
      }
   }
}
