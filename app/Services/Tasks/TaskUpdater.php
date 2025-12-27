<?php

namespace App\Services\Tasks;

use App\Http\Controllers\Traits\SyncsRelations;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TaskUpdater
{
   use SyncsRelations;

   /**
    * Update a task and propagate changes to its occurrences (snapshots, workers, visibility).
    */
   public function updateTask(Task $task, array $data, ?bool $requiresDocumentInput = null): Task
   {
      return DB::transaction(function () use ($task, $data, $requiresDocumentInput) {
         $latestOccurrence = $task->latestOccurrenceWithoutVisibility()->first();

         // Explicit value wins; otherwise inherit from latest occurrence (if any)
         $requiresDocument = $requiresDocumentInput;
         if ($requiresDocument === null) {
            $requiresDocument = optional($latestOccurrence)->requires_document;
         }

         $this->syncRelations($task, $data, [
            'users' => 'user_ids',
         ]);

         $task->update($data);
         $task->load('users');

         $recalculateDueDate = $task->wasChanged('recurrence_interval') || $task->wasChanged('is_recurring');

         $updateOccurrences = [];
         if ($requiresDocument !== null) {
            $updateOccurrences['requires_document'] = $requiresDocument;
         }

         if ($task->wasChanged('visibility')) {
            // Enum column expects "0"/"1"
            $updateOccurrences['visibility'] = $task->visibility ? '1' : '0';
         }

         if (!empty($updateOccurrences)) {
            $task->taskOccurrences()->update($updateOccurrences);
         }

         if ($latestOccurrence) {
            $latestUpdates = [
               'branch_id_snapshot' => $task->branch_id,
               'branch_name_snapshot' => $task->branch_name_snapshot,
               'service_id_snapshot' => $task->service_id,
               'service_name_snapshot' => $task->service_name_snapshot,
            ];

            if ($recalculateDueDate) {
               $interval = (int) ($task->recurrence_interval ?? 0);
               $latestUpdates['due_date'] = $task->is_recurring && $interval > 0
                  ? now()->addDays($interval)
                  : null;
            }

            $latestOccurrence->update($latestUpdates);

            // Sync latest occurrence workers to match task workers (snapshot)
            $latestOccurrence->workers()->delete();
            if ($task->users->isNotEmpty()) {
               $latestOccurrence->workers()->createMany(
                  $task->users->map(fn($user) => [
                     'worker_id_snapshot' => $user->id,
                     'worker_name_snapshot' => $user->full_name,
                  ])->all()
               );
            }
         }

         return $task;
      });
   }
}
