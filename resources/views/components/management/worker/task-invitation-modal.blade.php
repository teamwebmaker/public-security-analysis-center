@props(['task', 'workers'])

@php
    $assignedWorkerIds = $task->users->pluck('id');
    $pendingWorkerIds = $task->workerInvitations
        ->where('status', \App\Models\TaskWorkerInvitation::STATUS_PENDING)
        ->pluck('invited_worker_id');
    $availableWorkers = $workers->reject(
        fn($worker) => $assignedWorkerIds->contains($worker->id)
            || $pendingWorkerIds->contains($worker->id)
    );
@endphp

<div class="modal fade" id="taskInvitationModal_{{ $task->id }}" tabindex="-1"
    aria-labelledby="taskInvitationModalLabel_{{ $task->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="taskInvitationModalLabel_{{ $task->id }}">
                    <i class="bi bi-person-plus me-1"></i>
                    კოლეგის მოწვევა — #{{ $task->id }}
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="დახურვა"></button>
            </div>

            <form method="POST" action="{{ route('management.worker.tasks.invitations.store', $task) }}">
                @csrf
                <div class="modal-body">
                    @if ($availableWorkers->isNotEmpty())
                        <label for="invited_worker_id_{{ $task->id }}" class="form-label">სპეციალისტი</label>
                        <select id="invited_worker_id_{{ $task->id }}" name="invited_worker_id"
                            class="form-select" required>
                            <option value="" selected disabled>აირჩიეთ სპეციალისტი</option>
                            @foreach ($availableWorkers as $worker)
                                <option value="{{ $worker->id }}">{{ $worker->full_name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            სპეციალისტი სამუშაოზე მხოლოდ მოწვევის მიღების შემდეგ მიმაგრდება.
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            მოსაწვევი აქტიური სპეციალისტი ვერ მოიძებნა.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">დახურვა</button>
                    @if ($availableWorkers->isNotEmpty())
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i>
                            მოწვევის გაგზავნა
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
