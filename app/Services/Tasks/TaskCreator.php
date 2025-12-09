<?php

namespace App\Services\Tasks;

use App\Http\Controllers\Traits\SyncsRelations;
use App\Models\Task;
use App\Models\TaskOccurrence;
use App\Models\TaskOccurrenceStatus;
use Illuminate\Support\Facades\DB;

class TaskCreator
{
   use SyncsRelations;

   /**
    * Create task and its first pending occurrence inside a transaction.
    */
   public function createWithInitialOccurrence(array $data): Task
   {
      return DB::transaction(function () use ($data) {
         $task = Task::create($data);

         $this->syncRelations($task, $data, ['users' => 'user_ids',]);
         $this->createInitialOccurrence($task, $data);

         return $task;
      });
   }

   protected function createInitialOccurrence(Task $task, array $data): void
   {
      // Prevent duplicate initial occurrence if caller reuses the same task instance
      if ($task->taskOccurrences()->exists()) {
         return;
      }

      $pendingStatusId = TaskOccurrenceStatus::where('name', 'pending')->value('id');

      $isRecurring = (bool) ($data['is_recurring'] ?? false);
      $interval = (int) ($data['recurrence_interval'] ?? 0);

      $dueDate = $isRecurring && $interval > 0
         ? now()->addDays($interval)
         : null;

      // task_id is set by the relation; snapshots capture current task metadata
      $task->taskOccurrences()->create([
         'branch_id_snapshot' => $task->branch_id,
         'branch_name_snapshot' => $task->branch_name_snapshot,
         'service_id_snapshot' => $task->service_id,
         'service_name_snapshot' => $task->service_name_snapshot,
         'due_date' => $dueDate,
         'status_id' => $pendingStatusId,
         'requires_document' => (bool) ($data['requires_document'] ?? false),
         'visibility' => $task->visibility ?? '1',
      ]);
   }
}
