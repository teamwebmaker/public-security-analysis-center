<?php

namespace App\Services\Tasks;

use App\Models\Task;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class TaskSelfAssignmentService
{
    public function assign(Task $task, User $worker): bool
    {
        return DB::transaction(function () use ($task, $worker): bool {
            $task = $this->lockActiveTask($task);
            $alreadyAssigned = $task->users()->whereKey($worker->id)->exists();

            $task->users()->syncWithoutDetaching([$worker->id]);

            $occurrence = $task->latestOccurrence()->first();
            if (!$occurrence) {
                throw new DomainException('Active task occurrence could not be found.');
            }

            $occurrence->workers()->firstOrCreate(
                ['worker_id_snapshot' => $worker->id],
                ['worker_name_snapshot' => $worker->full_name]
            );

            return !$alreadyAssigned;
        });
    }

    public function remove(Task $task, User $worker): bool
    {
        return DB::transaction(function () use ($task, $worker): bool {
            $task = $this->lockActiveTask($task);
            $occurrence = $task->latestOccurrence()->first();

            if (!$occurrence) {
                throw new DomainException('Active task occurrence could not be found.');
            }

            $wasAssigned = $task->users()->whereKey($worker->id)->exists()
                || $occurrence->workers()->where('worker_id_snapshot', $worker->id)->exists();

            $task->users()->detach($worker->id);
            $occurrence->workers()->where('worker_id_snapshot', $worker->id)->delete();

            return $wasAssigned;
        });
    }

    private function lockActiveTask(Task $task): Task
    {
        $lockedTask = Task::query()
            ->whereKey($task->getKey())
            ->lockForUpdate()
            ->firstOrFail();

        $lockedTask->load('latestOccurrence.status');

        if (!$lockedTask->isActive()) {
            throw new DomainException('Only active tasks can be assigned or unassigned.');
        }

        return $lockedTask;
    }
}
