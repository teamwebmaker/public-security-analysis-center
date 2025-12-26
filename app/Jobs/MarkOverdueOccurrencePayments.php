<?php

namespace App\Jobs;

use App\Models\TaskOccurrence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MarkOverdueOccurrencePayments implements ShouldQueue
{
   use Dispatchable;
   use InteractsWithQueue;
   use Queueable;
   use SerializesModels;

   /**
    * Mark unpaid occurrences as overdue and log notifications.
    */
   public function handle(): void
   {
      $today = now('Asia/Tbilisi')->toDateString();

      TaskOccurrence::query()
         ->whereNotNull('due_date')
         ->whereDate('due_date', '<', $today)
         ->where('payment_status', 'unpaid')
         ->with(['task.branch.users'])
         ->chunkById(100, function ($occurrences) {
            foreach ($occurrences as $occurrence) {
               $updated = TaskOccurrence::query()
                  ->whereKey($occurrence->id)
                  ->where('payment_status', 'unpaid')
                  ->update(['payment_status' => 'overdue']);

               if ($updated === 0) {
                  continue;
               }

               $this->logForResponsiblePersons(
                  $occurrence,
                  'Payment status changed to overdue',
                  'Payment status changed to overdue'
               );
            }
         });
   }

   /**
    * Log notifications for responsible persons on a branch.
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
