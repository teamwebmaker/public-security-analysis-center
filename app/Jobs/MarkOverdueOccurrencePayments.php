<?php

namespace App\Jobs;

use App\Models\TaskOccurrence;
use App\Models\TaskOccurrenceStatus;
use App\Services\Sms\SmsLogService;
use App\Services\Sms\ResponsiblePersonSmsSender;
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
   public function handle(SmsLogService $smsLogService, ResponsiblePersonSmsSender $smsSender): void
   {
      $today = now('Asia/Tbilisi')->toDateString();
      $onHoldStatusId = TaskOccurrenceStatus::where('name', 'on_hold')->value('id');

      Log::info('MarkOverdueOccurrencePayments started', [
         'today' => $today,
         'on_hold_status_id' => $onHoldStatusId,
      ]);

      // Select overdue, unpaid occurrences (excluding cancelled) and process in chunks.
      TaskOccurrence::query()
         ->whereNotNull('due_date')
         // Due date must be before than today.
         ->whereDate('due_date', '<', $today)
         ->where('payment_status', 'unpaid')
         ->whereHas('status', fn($q) => $q->where('name', '!=', 'cancelled'))
         ->with(['task.branch.users.services'])
         ->chunkById(100, function ($occurrences) use ($smsLogService, $smsSender, $onHoldStatusId) {
            $byUser = [];
            $updatedCount = 0;
            $eventType = 'debt_overdue_service_suspended';

            foreach ($occurrences as $occurrence) {
               // Only change still-unpaid records to overdue (and on-hold if configured).
               $updateData = ['payment_status' => 'overdue'];
               if ($onHoldStatusId !== null) {
                  $updateData['status_id'] = $onHoldStatusId;
               }

               $updated = TaskOccurrence::query()
                  ->whereKey($occurrence->id)
                  ->where('payment_status', 'unpaid')
                  ->update($updateData);

               if ($updated === 0) {
                  continue;
               }
               $updatedCount++;

               // Collect responsible persons (branch users) for this occurrence.
               $users = $occurrence->task?->branch?->users ?? collect();

               if ($users->isEmpty()) {
                  Log::warning('Payment SMS skipped: no responsible persons found.', [
                     'event_type' => 'debt_overdue_service_suspended',
                     'occurrence_id' => $occurrence->id,
                     'due_date' => $occurrence->due_date?->toDateString(),
                  ]);
                  continue;
               }

               foreach ($users as $user) {
                  // Group occurrences per responsible person to send a single aggregated SMS.
                  $byUser[$user->id]['user'] = $user;
                  $byUser[$user->id]['occurrence_ids'][] = $occurrence->id;
                  $byUser[$user->id]['occurrence_service_ids'][$occurrence->id] = $occurrence->service_id_snapshot;
               }
            }

            Log::info('Overdue occurrences processed', [
               'updated_count' => $updatedCount,
               'unique_recipients' => count($byUser),
               'occurrences_in_chunk' => $occurrences->count(),
               'byUser' => $byUser,
            ]);

            $smsSender->sendAggregatedSmsToResponsiblePersons(
               $byUser,
               $smsLogService,
               $eventType,
               fn(array $occurrenceIds) => $this->buildOverdueMessage(occurrenceIds: $occurrenceIds),
               'Sending overdue SMS'
            );
         });

      Log::info('MarkOverdueOccurrencePayments finished');
   }

   private function buildOverdueMessage(array $occurrenceIds): string
   {
      $list = implode(', ', array_map(fn($id) => "#{$id}", $occurrenceIds));

      return "⛔ სამუშაოები შეჩერებულია გადაუხდელობის გამო.\n"
         . "სამუშაოები: {$list}\n"
         . "დაფარვის შემდეგ ადგება.";
   }
}
