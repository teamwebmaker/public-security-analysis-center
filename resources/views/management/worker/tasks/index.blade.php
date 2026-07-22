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

    <div class="d-flex flex-column  align-items-start flex-lg-row  align-items-lg-center justify-content-between">
        <x-shared.filter-bar :filters="$filters" :action="route('management.dashboard.tasks')" :resetUrl="route('management.dashboard.tasks')" />
        <x-shared.search-bar headingPosition="left" :action="route('management.dashboard.tasks')" :minLength="1" />
    </div>

    @if ($tasks->isNotEmpty())
        <x-shared.table :items="$tasks" :headers="$taskHeaders" :rows="$taskRows" :sortableMap="$sortableMap"
            :tooltipColumns="['branch', 'service']" :customActions="$taskActions" :modalTriggers="$workModalTriggers" />

        @foreach ($tasks as $task)
            @if ($task->latestOccurrence?->status?->name === 'in_progress' && $task->latestOccurrence?->requires_document)
                <x-management.worker.upload-modal :task="$task" />
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
