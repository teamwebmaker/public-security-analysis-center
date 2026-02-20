<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\TaskOccurrence;
use App\Models\TaskOccurrenceStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateTaskOccurrences implements ShouldQueue
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
    * Create new task occurrences when the latest due_date is reached.
    */
   public function handle(): void
   {
      Log::info('CreateTaskOccurrences job started.');
      $businessTimezone = config('app.business_timezone', 'Asia/Tbilisi');
      $today = now($businessTimezone)->toDateString();

      $createdCount = 0;
      $skippedFutureCount = 0;
      $skippedDuplicateCount = 0;

      try {
         $pendingStatusId = TaskOccurrenceStatus::where('name', 'pending')->value('id');
         if (!$pendingStatusId) {
            Log::warning('CreateTaskOccurrences job aborted: pending status not found.');
            return;
         }

         Task::query()
            ->where('is_recurring', true)
            ->where('recurrence_interval', '>', 0)
            ->where('archived', '0')
            ->whereHas('latestOccurrence', function ($query) use ($today) {
               $query->whereNotNull('due_date')
                  ->whereDate('due_date', '<=', $today);
            })
            ->with(['latestOccurrence.status', 'users'])
            ->chunkById(100, function ($tasks) use ($today, $pendingStatusId, &$createdCount, &$skippedFutureCount, &$skippedDuplicateCount) {
               foreach ($tasks as $task) {
                  try {
                     $latest = $task->latestOccurrence;
                     if (!$latest || !$latest->due_date) {
                        $skippedFutureCount++;
                        continue;
                     }

                     if ($latest->due_date->toDateString() > $today) {
                        $skippedFutureCount++;
                        continue;
                     }

                     $latestStatus = $latest->status?->name;
                     if (in_array($latestStatus, ['on_hold', 'cancelled'], true)) {
                        $skippedFutureCount++;
                        continue;
                     }

                     if ($latest->payment_status !== 'paid') {
                        $skippedFutureCount++;
                        continue;
                     }

                     $nextDueDate = $latest->due_date->copy()->addDays($task->recurrence_interval);

                     $alreadyScheduled = TaskOccurrence::query()
                        ->withoutGlobalScope('visible')
                        ->where('task_id', $task->id)
                        ->whereDate('due_date', $nextDueDate->toDateString())
                        ->exists();
                     if ($alreadyScheduled) {
                        $skippedDuplicateCount++;
                        continue;
                     }

                     try {
                        DB::transaction(function () use ($task, $latest, $pendingStatusId, $nextDueDate) {
                           $occurrence = $task->taskOccurrences()->create([
                              'branch_id_snapshot' => $task->branch_id,
                              'branch_name_snapshot' => $task->branch_name_snapshot,
                              'service_id_snapshot' => $task->service_id,
                              'service_name_snapshot' => $task->service_name_snapshot,
                              'due_date' => $nextDueDate,
                              'status_id' => $pendingStatusId,
                              'requires_document' => $latest->requires_document ?? true,
                              'visibility' => $task->visibility ?? '1',
                           ]);

                           if ($task->users->isNotEmpty()) {
                              $occurrence->workers()->createMany(
                                 $task->users
                                    ->map(fn($user) => [
                                       'worker_id_snapshot' => $user->id,
                                       'worker_name_snapshot' => $user->full_name,
                                    ])
                                    ->all()
                              );
                           }
                        });
                        $createdCount++;
                     } catch (QueryException $e) {
                        if ($this->isDueDateDuplicateException($e)) {
                           $skippedDuplicateCount++;
                           continue;
                        }

                        Log::error('CreateTaskOccurrences query failure for task', [
                           'task_id' => $task->id,
                           'error' => $e->getMessage(),
                        ]);
                        continue;
                     }
                  } catch (\Throwable $e) {
                     Log::error('CreateTaskOccurrences failed for task', [
                        'task_id' => $task->id,
                        'error' => $e->getMessage(),
                     ]);
                     continue;
                  }
               }
            });
      } catch (\Throwable $e) {
         Log::error('CreateTaskOccurrences job failed unexpectedly', [
            'error' => $e->getMessage(),
         ]);
      }

      Log::info('CreateTaskOccurrences job finished.', [
         'created' => $createdCount,
         'skipped_future' => $skippedFutureCount,
         'skipped_duplicate' => $skippedDuplicateCount,
      ]);
   }

   private function isDueDateDuplicateException(QueryException $e): bool
   {
      $message = strtolower($e->getMessage());
      $mentionsIndex = str_contains($message, 'task_occ_task_due_unique')
         || str_contains($message, 'task_occurrences_task_id_due_date_unique');

      if (!$mentionsIndex) {
         return false;
      }

      $sqlState = (string) ($e->errorInfo[0] ?? '');
      $driverCode = (int) ($e->errorInfo[1] ?? 0);

      return $sqlState === '23505' // PostgreSQL unique_violation
         || ($sqlState === '23000' && $driverCode === 1062); // MySQL duplicate entry
   }
}
