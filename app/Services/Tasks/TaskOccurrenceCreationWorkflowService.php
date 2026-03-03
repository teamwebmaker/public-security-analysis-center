<?php

namespace App\Services\Tasks;

use App\Models\Task;
use App\Models\TaskOccurrence;
use App\Services\Sms\WorkerTaskAssignmentSmsSender;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class TaskOccurrenceCreationWorkflowService
{
   public function __construct(
      private TaskOccurrenceCreator $occurrenceCreator,
      private WorkerTaskAssignmentSmsSender $workerSmsSender
   ) {
   }

   /**
    * Create an occurrence and notify workers after commit.
    */
   public function createAndNotify(Task $task, array $attributes): TaskOccurrence
   {
      $occurrence = $this->occurrenceCreator->createFromTask($task, $attributes);

      DB::afterCommit(function () use ($occurrence, $task): void {
         try {
            $this->workerSmsSender->sendForOccurrence($occurrence);
         } catch (Throwable $e) {
            Log::error('Worker assignment SMS dispatch failed after occurrence creation', [
               'task_id' => $task->id,
               'occurrence_id' => $occurrence->id ?? null,
               'error' => $e->getMessage(),
            ]);
         }
      });

      return $occurrence;
   }
}

