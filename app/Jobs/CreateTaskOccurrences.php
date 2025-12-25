<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\TaskOccurrenceStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateTaskOccurrences implements ShouldQueue
{
   use Dispatchable;
   use InteractsWithQueue;
   use Queueable;
   use SerializesModels;

   /**
    * Create new task occurrences when the latest due_date is reached.
    */
   public function handle(): void
   {
      // run command php artisan schedule:work
      // Log::info('CreateTaskOccurrences job started.');

      $pendingStatusId = TaskOccurrenceStatus::where('name', 'pending')->value('id');
      if (!$pendingStatusId) {
         Log::warning('CreateTaskOccurrences job aborted: pending status not found.');
         return;
      }

      Task::query()
         ->where('is_recurring', true)
         ->where('recurrence_interval', '>', 0)
         ->where('archived', '0')
         ->whereHas('latestOccurrence', function ($query) {
            $query->whereNotNull('due_date')
               ->whereDate('due_date', '<=', now()->toDateString());
         })
         ->with(['latestOccurrence', 'users'])
         ->chunkById(100, function ($tasks) use ($pendingStatusId) {
            foreach ($tasks as $task) {
               $latest = $task->latestOccurrence;
               if (!$latest || !$latest->due_date || $latest->due_date->isFuture()) {
                  continue;
               }

               $nextDueDate = $latest->due_date->copy()->addDays($task->recurrence_interval);

               $alreadyScheduled = $task->taskOccurrences()
                  ->whereDate('due_date', $nextDueDate->toDateString())
                  ->exists();
               if ($alreadyScheduled) {
                  return;
               }

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
            }
         });

      // Log::info('CreateTaskOccurrences job finished.');
   }
}
