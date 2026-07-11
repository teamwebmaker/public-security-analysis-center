@extends('management.master')

@section('title', 'სამუშაო სივრცე')

@section('main')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">სამუშაო სივრცე</h1>
            <p class="text-muted mb-0">მართეთ თქვენი სამუშაოები ან აირჩიეთ ახალი აქტიური სამუშაო.</p>
        </div>

        <a href="{{ route('management.worker.tasks.create') }}" class="btn btn-primary text-nowrap">
            <i class="bi bi-plus-circle me-1"></i>
            სამუშაოს შექმნა
        </a>
    </div>

    <nav class="mb-4" aria-label="სამუშაოების სექციები">
        <div class="nav nav-tabs flex-column flex-sm-row" role="tablist">
            <a href="{{ route('management.dashboard.tasks', ['tab' => 'mine']) }}"
                class="nav-link {{ $activeTab === 'mine' ? 'active' : '' }}"
                aria-current="{{ $activeTab === 'mine' ? 'page' : 'false' }}">
                <i class="bi bi-person-workspace me-1"></i>
                ჩემი სამუშაოები
                <span class="badge rounded-pill text-bg-secondary ms-1">{{ $myTasksCount }}</span>
            </a>

            <a href="{{ route('management.dashboard.tasks', ['tab' => 'available']) }}"
                class="nav-link {{ $activeTab === 'available' ? 'active' : '' }}"
                aria-current="{{ $activeTab === 'available' ? 'page' : 'false' }}">
                <i class="bi bi-collection me-1"></i>
                ხელმისაწვდომი სამუშაოები
                <span class="badge rounded-pill text-bg-secondary ms-1">{{ $availableTasksCount }}</span>
            </a>
        </div>
    </nav>



    <div class="d-flex flex-column  align-items-start flex-lg-row  align-items-lg-center justify-content-between">
        <x-shared.filter-bar :filters="$filters" :action="route('management.dashboard.tasks')" :preserve="['tab' => $activeTab]" :resetUrl="route('management.dashboard.tasks', ['tab' => $activeTab])" />
        <x-shared.search-bar headingPosition="left" :action="route('management.dashboard.tasks')" :preserve="['tab' => $activeTab]" :minLength="1" />
    </div>

    @if ($tasks->isNotEmpty())
        <x-shared.table :items="$tasks" :headers="$taskHeaders" :rows="$taskRows" :sortableMap="$sortableMap"
            :tooltipColumns="['branch', 'service']" :customActions="$taskActions" :modalTriggers="$workModalTriggers" />

        @if ($activeTab === 'mine')
            @foreach ($tasks as $task)
                @if ($task->latestOccurrence?->status?->name === 'in_progress' && $task->latestOccurrence?->requires_document)
                    <x-management.worker.upload-modal :task="$task" />
                @endif
            @endforeach
        @endif

        @if ($tasks->hasPages())
            <div class="mt-4">
                {{ $tasks->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @else
        <x-ui.empty-state-message message='სამუშაოები ვერ მოიძებნა' :resourceName="null" :overlay="false" />
    @endif
@endsection