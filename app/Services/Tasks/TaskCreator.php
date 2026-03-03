<?php

namespace App\Services\Tasks;

use App\Http\Controllers\Traits\SyncsRelations;
use App\Models\Task;
use App\Models\TaskOccurrenceStatus;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TaskCreator
{
   use SyncsRelations;

   public function __construct(
      private TaskOccurrenceCreationWorkflowService $occurrenceWorkflow
   ) {
   }

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
      if (!$pendingStatusId) {
         throw new RuntimeException('Task occurrence "pending" status is not configured.');
      }

      $isRecurring = (bool) ($data['is_recurring'] ?? false);
      $interval = (int) ($data['recurrence_interval'] ?? 0);
      $businessTimezone = config('app.business_timezone', config('app.timezone', 'UTC'));

      $dueDate = $isRecurring && $interval > 0
         ? now($businessTimezone)->addDays($interval)
         : null;

      $this->occurrenceWorkflow->createAndNotify($task, [
         'due_date' => $dueDate,
         'status_id' => $pendingStatusId,
         'requires_document' => (bool) ($data['requires_document'] ?? false),
         'visibility' => $task->visibility ?? '1',
      ]);
   }
}
