<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskWorkerInvitationRequest;
use App\Models\Task;
use App\Models\TaskWorkerInvitation;
use App\Services\Tasks\TaskWorkerInvitationService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class TaskWorkerInvitationController extends Controller
{
    public function __construct(
        private TaskWorkerInvitationService $invitationService
    ) {
    }

    public function store(
        StoreTaskWorkerInvitationRequest $request,
        Task $task
    ): RedirectResponse {
        try {
            $this->invitationService->invite(
                $task,
                $request->user(),
                (int) $request->validated('invited_worker_id')
            );
        } catch (DomainException $e) {
            return back()->withErrors(['invitation' => $e->getMessage()]);
        } catch (Throwable $e) {
            Log::error('Worker task invitation failed', [
                'task_id' => $task->id,
                'inviter_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['invitation' => 'მოწვევის გაგზავნა ვერ მოხერხდა.']);
        }

        return back()->with('success', 'სპეციალისტს მოწვევა გაეგზავნა.');
    }

    public function accept(Request $request, TaskWorkerInvitation $invitation): RedirectResponse
    {
        $this->authorize('respond', $invitation);

        try {
            $this->invitationService->accept($invitation, $request->user());
        } catch (DomainException $e) {
            return back()->withErrors(['invitation' => $e->getMessage()]);
        } catch (Throwable $e) {
            Log::error('Worker task invitation acceptance failed', [
                'invitation_id' => $invitation->id,
                'worker_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['invitation' => 'მოწვევის მიღება ვერ მოხერხდა.']);
        }

        return back()->with('success', 'მოწვევა მიღებულია და სამუშაოზე მიმაგრებული ხართ.');
    }

    public function decline(Request $request, TaskWorkerInvitation $invitation): RedirectResponse
    {
        $this->authorize('respond', $invitation);

        try {
            $this->invitationService->decline($invitation, $request->user());
        } catch (DomainException $e) {
            return back()->withErrors(['invitation' => $e->getMessage()]);
        } catch (Throwable $e) {
            Log::error('Worker task invitation decline failed', [
                'invitation_id' => $invitation->id,
                'worker_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['invitation' => 'მოწვევის უარყოფა ვერ მოხერხდა.']);
        }

        return back()->with('success', 'მოწვევა უარყოფილია.');
    }
}
