<?php

namespace App\Services\Tasks;

use App\Models\Task;
use App\Models\TaskOccurrence;

class TaskOccurrenceCreator
{
   /**
    * Create an occurrence from task snapshots and copy current task workers.
    */
   public function createFromTask(Task $task, array $attributes): TaskOccurrence
   {
      $occurrence = $task->taskOccurrences()->create([
         'branch_id_snapshot' => $task->branch_id,
         'branch_name_snapshot' => $task->branch_name_snapshot,
         'service_id_snapshot' => $task->service_id,
         'service_name_snapshot' => $task->service_name_snapshot,
         'visibility' => $task->visibility ?? '1',
         ...$attributes,
      ]);

      $workers = $task->relationLoaded('users')
         ? $task->users
         : $task->users()->get(['id', 'full_name']);

      if ($workers->isNotEmpty()) {
         $occurrence->workers()->createMany(
            $workers->map(fn($user) => [
               'worker_id_snapshot' => $user->id,
               'worker_name_snapshot' => $user->full_name,
            ])->all()
         );
      }

      return $occurrence;
   }
}
