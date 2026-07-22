<?php

namespace App\Services\Tasks;

use App\Models\Task;
use App\Models\TaskWorkerInvitation;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class TaskWorkerInvitationService
{
    public function invite(Task $task, User $inviter, int $invitedWorkerId): TaskWorkerInvitation
    {
        return DB::transaction(function () use ($task, $inviter, $invitedWorkerId): TaskWorkerInvitation {
            $task = $this->lockTask($task->id);

            if ((int) $task->created_by_user_id !== (int) $inviter->id || !$task->isActive()) {
                throw new DomainException('ამ სამუშაოზე თანამშრომლის მოწვევის უფლება არ გაქვთ.');
            }

            $invitedWorker = User::query()
                ->with('role')
                ->lockForUpdate()
                ->findOrFail($invitedWorkerId);

            if ($invitedWorker->getRoleName() !== 'worker' || !$invitedWorker->is_active) {
                throw new DomainException('მოწვევა მხოლოდ აქტიურ სპეციალისტთან არის შესაძლებელი.');
            }

            if ((int) $invitedWorker->id === (int) $inviter->id) {
                throw new DomainException('საკუთარი თავის მოწვევა შეუძლებელია.');
            }

            if ($task->users()->whereKey($invitedWorker->id)->exists()) {
                throw new DomainException('ეს სპეციალისტი უკვე მიმაგრებულია სამუშაოზე.');
            }

            return TaskWorkerInvitation::query()->updateOrCreate(
                [
                    'task_id' => $task->id,
                    'invited_worker_id' => $invitedWorker->id,
                ],
                [
                    'inviter_id' => $inviter->id,
                    'status' => TaskWorkerInvitation::STATUS_PENDING,
                    'responded_at' => null,
                ]
            );
        });
    }

    public function accept(TaskWorkerInvitation $invitation, User $worker): void
    {
        DB::transaction(function () use ($invitation, $worker): void {
            $task = $this->lockTask($invitation->task_id);
            $invitation = $this->lockInvitation($invitation->id, $task->id);

            $this->ensurePendingInvitationBelongsTo($invitation, $worker);

            if (!$task->isActive()) {
                throw new DomainException('არააქტიურ სამუშაოზე მოწვევის მიღება შეუძლებელია.');
            }

            $occurrence = $task->latestOccurrence()->first();
            if (!$occurrence) {
                throw new DomainException('აქტიური სამუშაო ციკლი ვერ მოიძებნა.');
            }

            $task->users()->syncWithoutDetaching([$worker->id]);
            $occurrence->workers()->firstOrCreate(
                ['worker_id_snapshot' => $worker->id],
                ['worker_name_snapshot' => $worker->full_name]
            );

            $invitation->update([
                'status' => TaskWorkerInvitation::STATUS_ACCEPTED,
                'responded_at' => now(),
            ]);
        });
    }

    public function decline(TaskWorkerInvitation $invitation, User $worker): void
    {
        DB::transaction(function () use ($invitation, $worker): void {
            $task = $this->lockTask($invitation->task_id);
            $invitation = $this->lockInvitation($invitation->id, $task->id);

            $this->ensurePendingInvitationBelongsTo($invitation, $worker);

            $invitation->update([
                'status' => TaskWorkerInvitation::STATUS_DECLINED,
                'responded_at' => now(),
            ]);
        });
    }

    private function lockTask(int $taskId): Task
    {
        return Task::query()
            ->whereKey($taskId)
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function lockInvitation(int $invitationId, int $taskId): TaskWorkerInvitation
    {
        return TaskWorkerInvitation::query()
            ->whereKey($invitationId)
            ->where('task_id', $taskId)
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function ensurePendingInvitationBelongsTo(
        TaskWorkerInvitation $invitation,
        User $worker
    ): void {
        if ((int) $invitation->invited_worker_id !== (int) $worker->id) {
            throw new DomainException('ამ მოწვევაზე პასუხის უფლება არ გაქვთ.');
        }

        if ($invitation->status !== TaskWorkerInvitation::STATUS_PENDING) {
            throw new DomainException('ამ მოწვევაზე პასუხი უკვე დაფიქსირებულია.');
        }
    }
}
