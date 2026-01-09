<?php

namespace App\Jobs;

use App\Models\TaskOccurrence;
use App\Services\Sms\SmsLogService;
use App\Services\Sms\ResponsiblePersonSmsSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
   public function handle(SmsLogService $smsLogService, ResponsiblePersonSmsSender $smsSender): void
   {
      $reminderDate = now('Asia/Tbilisi')->addDays(2)->toDateString();
      Log::info('SendUpcomingPaymentReminders started', [
         'reminder_date' => $reminderDate,
      ]);

      // Select unpaid occurrences due in 2 days and process in chunks.
      TaskOccurrence::query()
         ->whereDate('due_date', '=', $reminderDate)
         ->where('payment_status', 'unpaid')
         ->whereHas('status', fn($q) => $q->where('name', '!=', 'cancelled'))
         ->with(['task.branch.users.services'])
         ->chunkById(100, function ($occurrences) use ($smsLogService, $smsSender, $reminderDate) {
            $eventType = 'debt_due_2_days';
            $byUser = [];

            foreach ($occurrences as $occurrence) {
               // Responsible persons come from the occurrence's branch users.
               $users = $occurrence->task?->branch?->users ?? collect();
               if ($users->isEmpty()) {
                  Log::warning('Payment SMS skipped: no responsible persons found.', [
                     'event_type' => $eventType,
                     'occurrence_id' => $occurrence->id,
                     'due_date' => $occurrence->due_date?->toDateString(),
                  ]);
                  continue;
               }

               foreach ($users as $user) {
                  // Group occurrences per user to send a single aggregated SMS.
                  $byUser[$user->id]['user'] = $user;
                  $byUser[$user->id]['occurrence_ids'][] = $occurrence->id;
                  $byUser[$user->id]['occurrence_service_ids'][$occurrence->id] = $occurrence->service_id_snapshot;
               }
            }

            Log::info('Upcoming reminders processed', [
               'occurrences_in_chunk' => $occurrences->count(),
               'unique_recipients' => count($byUser),
            ]);

            $smsSender->sendAggregatedSmsToResponsiblePersons(
               $byUser,
               $smsLogService,
               $eventType,
               fn(array $occurrenceIds) => $this->buildUpcomingReminderMessage($occurrenceIds, $reminderDate),
               'Sending upcoming payment SMS',
               [],
               [
                  'due_date' => $reminderDate,
               ]
            );
         });

      Log::info('SendUpcomingPaymentReminders finished');
   }

   private function buildUpcomingReminderMessage(array $occurrenceIds, string $reminderDate): string
   {
      $list = implode(', ', array_map(fn($id) => "#{$id}", $occurrenceIds));
      $dueDate = $reminderDate ? date('d.m.Y', strtotime($reminderDate)) : '—';

      return "⚠️ გადახდის შეხსენება\n"
         . "ბოლო თარიღი: {$dueDate}\n"
         . "სამუშაოები: {$list}";
   }
}
