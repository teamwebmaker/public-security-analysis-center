@extends('management.master')

@section('title', 'სამუშაო სივრცე')

@section('main')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">სამუშაო სივრცე</h1>
            <p class="text-muted mb-0">მართეთ თქვენზე განსაზღვრული აქტიური სამუშაოები.</p>
        </div>

        <a href="{{ route('management.worker.tasks.create') }}" class="btn btn-primary text-nowrap">
            <i class="bi bi-plus-circle me-1"></i>
            სამუშაოს შექმნა
        </a>
    </div>

    @if ($pendingInvitations->isNotEmpty())
        <section class="card border-primary-subtle shadow-sm mb-4" aria-labelledby="pending-invitations-heading">
            <div class="card-header bg-primary bg-opacity-10 border-primary-subtle">
                <h2 id="pending-invitations-heading" class="h5 mb-0">
                    <i class="bi bi-envelope-paper me-1"></i>
                    სამუშაოზე მოწვევები
                    <span class="badge text-bg-primary ms-1">{{ $pendingInvitations->count() }}</span>
                </h2>
            </div>
            <div class="list-group list-group-flush">
                @foreach ($pendingInvitations as $invitation)
                    <div class="list-group-item d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 py-3">
                        <div>
                            <div class="fw-semibold">
                                #{{ $invitation->task_id }} —
                                {{ $invitation->task?->service_name_snapshot ?? '---' }}
                            </div>
                            <div class="small text-muted mt-1">
                                <span class="me-3">
                                    <i class="bi bi-building me-1"></i>
                                    {{ $invitation->task?->branch_name_snapshot ?? '---' }}
                                </span>
                                <span>
                                    <i class="bi bi-person me-1"></i>
                                    მომწვევი: {{ $invitation->inviter?->full_name ?? '---' }}
                                </span>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('management.worker.task-invitations.accept', $invitation) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-check-circle me-1"></i>
                                    მიღება
                                </button>
                            </form>
                            <form method="POST" action="{{ route('management.worker.task-invitations.decline', $invitation) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('ნამდვილად გსურთ მოწვევის უარყოფა?')">
                                    <i class="bi bi-x-circle me-1"></i>
                                    უარყოფა
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <div class="d-flex flex-column  align-items-start flex-lg-row  align-items-lg-center justify-content-between">
        <x-shared.filter-bar :filters="$filters" :action="route('management.dashboard.tasks')" :resetUrl="route('management.dashboard.tasks')" />
        <x-shared.search-bar headingPosition="left" :action="route('management.dashboard.tasks')" :minLength="1" />
    </div>

    <x-shared.result-count :count="$tasks->total()" />

    @if ($tasks->isNotEmpty())
        <x-shared.table :items="$tasks" :headers="$taskHeaders" :rows="$taskRows" :sortableMap="$sortableMap"
            :tooltipColumns="['branch', 'service']" :customActions="$taskActions" :modalTriggers="$taskModalTriggers" />

        @foreach ($tasks as $task)
            @if ($task->latestOccurrence?->status?->name === 'in_progress' && $task->latestOccurrence?->requires_document)
                <x-management.worker.upload-modal :task="$task" />
            @endif

            @if ((int) $task->created_by_user_id === (int) auth()->id())
                <x-management.worker.task-invitation-modal :task="$task" :workers="$inviteableWorkers" />
            @endif
        @endforeach

        @if ($tasks->hasPages())
            <div class="mt-4">
                {{ $tasks->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @else
        <x-ui.empty-state-message message='სამუშაოები ვერ მოიძებნა' :resourceName="null" :overlay="false" />
    @endif
@endsection
