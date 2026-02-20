<?php

namespace App\Jobs;

use App\Models\TaskOccurrence;
use App\Models\TaskOccurrenceStatus;
use App\Services\Messages\MessageStoreService;
use App\Services\Sms\SmsLogService;
use App\Services\Sms\ResponsiblePersonSmsSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MarkOverdueOccurrencePayments implements ShouldQueue
{
   use Dispatchable;
   use InteractsWithQueue;
   use Queueable;
   use SerializesModels;

   public function middleware(): array
   {
      return [
         (new WithoutOverlapping('occurrences:daily-chain'))
            ->shared()
            ->releaseAfter(60)
            ->expireAfter(10800),
      ];
   }

   /**
    * Mark unpaid occurrences as overdue and log notifications.
    */
   public function handle(
      SmsLogService $smsLogService,
      ResponsiblePersonSmsSender $smsSender,
      MessageStoreService $messageStoreService
   ): void {
      $businessTimezone = config('app.business_timezone', 'Asia/Tbilisi');
      $today = now($businessTimezone)->toDateString();
      $onHoldStatusId = null;
      $skippedSummary = [
         'no_responsible_persons' => [],
         'missing_phone' => [],
         'not_authorized' => [],
         'system_errors' => [],
      ];

      try {
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
            ->chunkById(100, function ($occurrences) use ($smsLogService, $smsSender, $onHoldStatusId, &$skippedSummary) {
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
                     $skippedSummary['no_responsible_persons'][] = $occurrence->id;
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
               ]);

               try {
                  $chunkSummary = $smsSender->sendAggregatedSmsToResponsiblePersons(
                     $byUser,
                     $smsLogService,
                     $eventType,
                     fn(array $occurrenceIds) => $this->buildOverdueMessage(occurrenceIds: $occurrenceIds),
                     'Sending overdue SMS'
                  );
                  $skippedSummary = $this->mergeSkippedSummary($skippedSummary, $chunkSummary['skipped'] ?? []);
               } catch (\Throwable $e) {
                  $occurrenceIds = $occurrences->pluck('id')->all();
                  $skippedSummary['system_errors'][] = [
                     'occurrence_ids' => $occurrenceIds,
                     'error' => $e->getMessage(),
                  ];

                  Log::error('Failed to send overdue payment SMS for chunk', [
                     'event_type' => $eventType,
                     'occurrence_ids' => $occurrenceIds,
                     'error' => $e->getMessage(),
                  ]);
               }
            });
      } catch (\Throwable $e) {
         $skippedSummary['system_errors'][] = [
            'occurrence_ids' => [],
            'error' => $e->getMessage(),
         ];
         Log::error('MarkOverdueOccurrencePayments failed unexpectedly', [
            'error' => $e->getMessage(),
         ]);
      }

      if ($this->hasSkippedSummary($skippedSummary)) {
         try {
            $messageStoreService->createAndDispatch([
               'source' => 'system',
               'subject' => 'ვადაგადაცილებული გადახდები ვერ გაიგზავნა',
               'message' => $this->buildSkippedSummaryMessage(
                  'ვადაგადაცილებული გადახდების შეტყობინება ვერ გაიგზავნა შემდეგი მიზეზებით:',
                  $skippedSummary
               ),
            ]);
         } catch (\Throwable $e) {
            Log::error('Failed to store overdue-payment system message', [
               'error' => $e->getMessage(),
            ]);
         }
      }

      Log::info('MarkOverdueOccurrencePayments finished');
   }

   private function buildOverdueMessage(array $occurrenceIds): string
   {
      $list = implode(', ', array_map(fn($id) => "#{$id}", $occurrenceIds));

      return "⛔ სამუშაოები შეჩერებულია გადაუხდელობის გამო.\n"
         . "სამუშაოები: {$list}\n"
         . "დაფარვის შემდეგ ადგება.";
   }

   private function buildSkippedSummaryMessage(string $header, array $skippedSummary): string
   {
      $lines = [$header];
      $noPersons = array_values(array_unique($skippedSummary['no_responsible_persons'] ?? []));
      if (!empty($noPersons)) {
         $list = implode(', ', array_map(fn($id) => "#{$id}", $noPersons));
         $lines[] = "პასუხისმგებელი პირები ვერ მოიძებნა: {$list}";
      }

      $missingPhone = $skippedSummary['missing_phone'] ?? [];
      if (!empty($missingPhone)) {
         $lines[] = 'ტელეფონი არ არის მითითებული:';
         foreach ($missingPhone as $entry) {
            $name = trim((string) ($entry['full_name'] ?? 'უცნობი'));
            $occurrenceIds = $entry['occurrence_ids'] ?? [];
            if (empty($occurrenceIds)) {
               continue;
            }
            $list = implode(', ', array_map(fn($id) => "#{$id}", $occurrenceIds));
            $lines[] = "{$name}: {$list}";
         }
      }

      $notAuthorized = $skippedSummary['not_authorized'] ?? [];
      if (!empty($notAuthorized)) {
         $lines[] = 'პასუხისმგებელი პირი არ არის ავტორიზებული SMS შეტყობინებებზე:';
         foreach ($notAuthorized as $entry) {
            $name = trim((string) ($entry['full_name'] ?? 'უცნობი'));
            $occurrenceIds = $entry['occurrence_ids'] ?? [];
            if (empty($occurrenceIds)) {
               continue;
            }
            $list = implode(', ', array_map(fn($id) => "#{$id}", $occurrenceIds));
            $lines[] = "{$name}: {$list}";
         }
      }

      $systemErrors = $skippedSummary['system_errors'] ?? [];
      if (!empty($systemErrors)) {
         $lines[] = 'SMS გაგზავნისას დაფიქსირდა სისტემური შეცდომა:';
         foreach ($systemErrors as $entry) {
            $occurrenceIds = $entry['occurrence_ids'] ?? [];
            $error = trim((string) ($entry['error'] ?? 'უცნობი შეცდომა'));
            $error = mb_substr($error, 0, 180);

            if (!empty($occurrenceIds)) {
               $list = implode(', ', array_map(fn($id) => "#{$id}", $occurrenceIds));
               $lines[] = "{$list}: {$error}";
            } else {
               $lines[] = $error;
            }
         }
      }

      return implode("\n", $lines);
   }

   private function mergeSkippedSummary(array $all, array $chunk): array
   {
      $all['no_responsible_persons'] = array_values(array_unique(array_merge(
         $all['no_responsible_persons'] ?? [],
         $chunk['no_responsible_persons'] ?? []
      )));

      foreach ($chunk['missing_phone'] ?? [] as $userId => $entry) {
         $all['missing_phone'][$userId]['full_name'] = $entry['full_name'] ?? ($all['missing_phone'][$userId]['full_name'] ?? 'უცნობი');
         $all['missing_phone'][$userId]['phone'] = $entry['phone'] ?? ($all['missing_phone'][$userId]['phone'] ?? '');
         $all['missing_phone'][$userId]['occurrence_ids'] = array_values(array_unique(array_merge(
            $all['missing_phone'][$userId]['occurrence_ids'] ?? [],
            $entry['occurrence_ids'] ?? []
         )));
      }

      foreach ($chunk['not_authorized'] ?? [] as $userId => $entry) {
         $all['not_authorized'][$userId]['full_name'] = $entry['full_name'] ?? ($all['not_authorized'][$userId]['full_name'] ?? 'უცნობი');
         $all['not_authorized'][$userId]['occurrence_ids'] = array_values(array_unique(array_merge(
            $all['not_authorized'][$userId]['occurrence_ids'] ?? [],
            $entry['occurrence_ids'] ?? []
         )));
      }

      $all['system_errors'] = array_values(array_merge(
         $all['system_errors'] ?? [],
         $chunk['system_errors'] ?? []
      ));

      return $all;
   }

   private function hasSkippedSummary(array $skippedSummary): bool
   {
      return !empty($skippedSummary['no_responsible_persons'])
         || !empty($skippedSummary['missing_phone'])
         || !empty($skippedSummary['not_authorized'])
         || !empty($skippedSummary['system_errors']);
   }
}
