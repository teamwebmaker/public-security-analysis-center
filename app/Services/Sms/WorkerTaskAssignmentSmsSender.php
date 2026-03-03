<?php

namespace App\Services\Sms;

use App\Models\SmsLog;
use App\Models\TaskOccurrence;
use App\Models\User;
use App\Services\Messages\MessageStoreService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Log;
use Throwable;

class WorkerTaskAssignmentSmsSender
{
   public function __construct(
      private SmsLogService $smsLogService,
      private MessageStoreService $messageStoreService
   ) {
   }

   /**
    * Send "task assigned" SMS to workers attached to the occurrence.
    * Missing worker phones are reported via system message.
    */
   public function sendForOccurrence(TaskOccurrence $occurrence): array
   {
      $occurrence->loadMissing('workers');

      // Skip if there are no workers
      if ($occurrence->workers->isEmpty()) {
         return $this->emptySummary();
      }

      // Get worker IDs
      $workerIds = $occurrence->workers
         ->pluck('worker_id_snapshot')
         ->filter()
         ->unique()
         ->values();

      // Get worker users 
      $usersById = User::query()
         ->whereIn('id', $workerIds)
         ->get(['id', 'full_name', 'phone'])
         ->keyBy('id');

      $message = $this->buildMessage($occurrence);
      $smsno = SmsLog::smsnoTypeNumber('information') ?? 2;

      $sent = 0;
      $failed = 0;
      $skippedDuplicate = 0;
      $missingPhone = [];

      foreach ($occurrence->workers as $workerSnapshot) {
         $workerId = (int) ($workerSnapshot->worker_id_snapshot ?? 0);
         $displayName = trim((string) ($workerSnapshot->worker_name_snapshot ?? 'უცნობი'));
         $user = $usersById->get($workerId);
         $destination = trim((string) ($user?->phone ?? ''));

         if ($destination === '') {
            $missingPhone[$workerId ?: ('snapshot_' . $workerSnapshot->id)] = [
               'worker_id' => $workerId ?: null,
               'full_name' => $displayName,
            ];
            continue;
         }

         try {
            $alreadySent = $this->smsLogService->alreadySent(
               $destination,
               'task_assigned',
               (int) $occurrence->id,
               'worker'
            );

            if ($alreadySent) {
               $skippedDuplicate++;
               continue;
            }

            $result = $this->smsLogService->sendEventNotification(
               $destination,
               $message,
               'task_assigned',
               (int) $occurrence->id,
               'worker',
               $smsno
            );

            if (($result['failed'] ?? false) === true) {
               $failed++;
            } else {
               $sent++;
            }
         } catch (Throwable $e) {
            $failed++;
            Log::error('Worker assignment SMS unexpected failure', [
               'occurrence_id' => $occurrence->id,
               'worker_id_snapshot' => $workerId ?: null,
               'phone' => $destination,
               'error' => $e->getMessage(),
            ]);
         }
      }

      if (!empty($missingPhone)) {
         $this->createMissingPhoneSystemMessage($occurrence, array_values($missingPhone));
      }

      return [
         'sent' => $sent,
         'failed' => $failed,
         'skipped_duplicate' => $skippedDuplicate,
         'missing_phone' => count($missingPhone),
      ];
   }

   /**
    * Aggregate multiple newly created occurrences and send one SMS per worker.
    *
    * @param array<int, TaskOccurrence> $occurrences
    */
   public function sendAggregatedForOccurrences(array $occurrences): array
   {
      $occurrenceCollection = $this->toOccurrenceCollection($occurrences);
      if ($occurrenceCollection->isEmpty()) {
         return $this->emptySummary();
      }

      $byWorker = [];

      foreach ($occurrenceCollection as $occurrence) {
         foreach ($occurrence->workers as $workerSnapshot) {
            $workerId = (int) ($workerSnapshot->worker_id_snapshot ?? 0);
            $workerKey = $workerId > 0
               ? 'id_' . $workerId
               : 'snapshot_' . $occurrence->id . '_' . $workerSnapshot->id;

            if (!isset($byWorker[$workerKey])) {
               $byWorker[$workerKey] = [
                  'worker_id' => $workerId > 0 ? $workerId : null,
                  'full_name' => trim((string) ($workerSnapshot->worker_name_snapshot ?? 'უცნობი')),
                  'occurrences' => [],
               ];
            }

            $byWorker[$workerKey]['occurrences'][(int) $occurrence->id] = [
               'id' => (int) $occurrence->id,
               'due_date' => $occurrence->due_date?->format('d.m.Y') ?? '—',
            ];
         }
      }

      if (empty($byWorker)) {
         return $this->emptySummary();
      }

      $workerIds = collect($byWorker)
         ->pluck('worker_id')
         ->filter()
         ->unique()
         ->values();

      $usersById = User::query()
         ->whereIn('id', $workerIds)
         ->get(['id', 'full_name', 'phone'])
         ->keyBy('id');

      $smsno = SmsLog::smsnoTypeNumber('information') ?? 2;
      $sent = 0;
      $failed = 0;
      $skippedDuplicate = 0;
      $missingPhone = [];

      foreach ($byWorker as $entry) {
         $workerId = $entry['worker_id'];
         $fullName = trim((string) ($entry['full_name'] ?? 'უცნობი'));
         $destination = trim((string) ($workerId ? ($usersById->get($workerId)?->phone ?? '') : ''));
         $occurrenceItems = array_values($entry['occurrences'] ?? []);
         $occurrenceIds = array_values(array_unique(array_map(fn($item) => (int) ($item['id'] ?? 0), $occurrenceItems)));
         $occurrenceIds = array_values(array_filter($occurrenceIds));

         if ($destination === '') {
            $missingPhone[] = [
               'worker_id' => $workerId,
               'full_name' => $fullName,
               'occurrence_ids' => $occurrenceIds,
            ];
            continue;
         }

         try {
            $unsentIds = [];
            foreach ($occurrenceIds as $occurrenceId) {
               $alreadySent = $this->smsLogService->alreadySent(
                  $destination,
                  'task_assigned',
                  $occurrenceId,
                  'worker'
               );

               if ($alreadySent) {
                  $skippedDuplicate++;
                  continue;
               }

               $unsentIds[] = $occurrenceId;
            }

            if (empty($unsentIds)) {
               continue;
            }

            $message = $this->buildAggregatedMessage($occurrenceItems, $unsentIds);
            $result = $this->smsLogService->sendEventNotificationForEntities(
               $destination,
               $message,
               'task_assigned',
               $unsentIds,
               'worker',
               $smsno
            );

            if (($result['failed'] ?? false) === true) {
               $failed++;
            } else {
               $sent++;
            }
         } catch (Throwable $e) {
            $failed++;
            Log::error('Worker aggregated assignment SMS unexpected failure', [
               'worker_id' => $workerId,
               'phone' => $destination,
               'occurrence_ids' => $unsentIds,
               'error' => $e->getMessage(),
            ]);
         }
      }

      if (!empty($missingPhone)) {
         $this->createMissingPhoneSystemMessageForAggregated($missingPhone);
      }

      return [
         'sent' => $sent,
         'failed' => $failed,
         'skipped_duplicate' => $skippedDuplicate,
         'missing_phone' => count($missingPhone),
      ];
   }

   /**
    * Load occurrences by IDs and send one aggregated SMS per worker.
    *
    * @param array<int, int|string> $occurrenceIds
    */
   public function sendAggregatedForOccurrenceIds(array $occurrenceIds): array
   {
      $occurrenceIds = array_values(array_unique(array_filter(array_map('intval', $occurrenceIds))));
      if (empty($occurrenceIds)) {
         return $this->emptySummary();
      }

      $occurrences = new EloquentCollection();
      foreach (array_chunk($occurrenceIds, 500) as $idChunk) {
         $chunk = TaskOccurrence::query()
            ->withoutGlobalScope('visible')
            ->whereIn('id', $idChunk)
            ->with('workers')
            ->get();
         $occurrences = $occurrences->merge($chunk);
      }

      if ($occurrences->isEmpty()) {
         return $this->emptySummary();
      }

      return $this->sendAggregatedForOccurrences($occurrences->all());
   }

   private function buildMessage(TaskOccurrence $occurrence): string
   {
      $dueDate = $occurrence->due_date?->format('d.m.Y') ?? '—';

      return "ახალი სამუშაო დაგენიშნათ.\n"
         . "#{$occurrence->id}\n"
         . "ბოლო ვადა: {$dueDate}\n";
   }

   private function createMissingPhoneSystemMessage(TaskOccurrence $occurrence, array $workers): void
   {
      $dueDate = $occurrence->due_date?->format('d.m.Y') ?? '—';
      $lines = [
         "მუშას SMS ვერ გაეგზავნა (ტელეფონი არ არის მითითებული).",
         "ციკლი: #{$occurrence->id}",
         "ვადა: {$dueDate}",
         'მუშები:',
      ];

      foreach ($workers as $worker) {
         $name = trim((string) ($worker['full_name'] ?? 'უცნობი'));
         $workerId = $worker['worker_id'] ?? null;
         $lines[] = $workerId ? "{$name} (ID: {$workerId})" : $name;
      }

      try {
         $this->messageStoreService->createAndDispatch([
            'source' => 'system',
            'subject' => 'მუშის ნომერი არ არის მითითებული',
            'message' => implode("\n", $lines),
         ]);
      } catch (Throwable $e) {
         Log::error('Failed to create system message for missing worker phone', [
            'occurrence_id' => $occurrence->id,
            'error' => $e->getMessage(),
         ]);
      }
   }

   private function buildAggregatedMessage(array $occurrenceItems, array $unsentIds): string
   {
      $unsentSet = array_fill_keys($unsentIds, true);
      $selected = collect($occurrenceItems)
         ->filter(fn($item) => isset($unsentSet[(int) ($item['id'] ?? 0)]))
         ->sortBy('id')
         ->values();

      $visibleLimit = 8;
      $visible = $selected->take($visibleLimit);
      $hiddenCount = $selected->count() - $visible->count();

      $list = $visible
         ->map(function ($item) {
            $id = (int) ($item['id'] ?? 0);
            $dueDate = (string) ($item['due_date'] ?? '—');
            return "#{$id} ({$dueDate})";
         })
         ->implode(', ');

      if ($hiddenCount > 0) {
         $list .= " +{$hiddenCount}";
      }

      return "ახალი სამუშაოები დაგენიშნათ:\n"
         . "{$list}\n";
   }

   private function createMissingPhoneSystemMessageForAggregated(array $workers): void
   {
      $lines = [
         "მუშებს SMS ვერ გაეგზავნა (ტელეფონი არ არის მითითებული).",
      ];

      foreach ($workers as $worker) {
         $name = trim((string) ($worker['full_name'] ?? 'უცნობი'));
         $workerId = $worker['worker_id'] ?? null;
         $occurrenceIds = array_values(array_unique($worker['occurrence_ids'] ?? []));
         $list = empty($occurrenceIds)
            ? '—'
            : implode(', ', array_map(fn($id) => "#{$id}", $occurrenceIds));

         $lines[] = ($workerId ? "{$name} (ID: {$workerId})" : $name) . ": {$list}";
      }

      try {
         $this->messageStoreService->createAndDispatch([
            'source' => 'system',
            'subject' => 'მუშის ნომერი არ არის მითითებული',
            'message' => implode("\n", $lines),
         ]);
      } catch (Throwable $e) {
         Log::error('Failed to create aggregated system message for missing worker phone', [
            'error' => $e->getMessage(),
         ]);
      }
   }

   /**
    * Normalize to an Eloquent collection and eager-load workers in one query.
    *
    * @param array<int, mixed> $occurrences
    */
   private function toOccurrenceCollection(array $occurrences): EloquentCollection
   {
      $items = array_values(array_filter(
         $occurrences,
         fn($item) => $item instanceof TaskOccurrence
      ));

      $collection = new EloquentCollection($items);
      if ($collection->isNotEmpty()) {
         $collection->loadMissing('workers');
      }

      return $collection;
   }

   private function emptySummary(): array
   {
      return [
         'sent' => 0,
         'failed' => 0,
         'skipped_duplicate' => 0,
         'missing_phone' => 0,
      ];
   }
}
