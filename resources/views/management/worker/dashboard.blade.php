@extends('management.master')

@section('title', 'მონაცემთა პანელი')

@section('main')
    <div class="row g-3 border-bottom pb-4">
        <x-management.stat-card label="ელოდება სამუშაოს დაწყებას" :count="$statusCounts['pending'] ?? 0"
            icon="bi bi-hourglass-split" iconWrapperClasses="bg-warning bg-opacity-10 text-warning" />

        <x-management.stat-card label="მიმდინარე სამუშაოები" :count="$statusCounts['in_progress'] ?? 0"
            icon="bi bi-ui-radios" iconWrapperClasses="bg-info bg-opacity-10 text-info" />

        <x-management.stat-card label="დასრულებული სამუშაოები" :count="$statusCounts['completed'] ?? 0"
            icon="bi bi-check-circle" iconWrapperClasses="bg-success bg-opacity-10 text-success" />

        <x-management.stat-card label="შეჩერებული სამუშაოები" :count="$statusCounts['on_hold'] ?? 0"
            icon="bi bi-pause-circle" iconWrapperClasses="bg-secondary bg-opacity-10 text-secondary" />
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body p-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                            <i class="bi bi-briefcase fs-3"></i>
                        </div>
                        <div>
                            <h2 class="h5 mb-2">ერთიანი სამუშაო სივრცე</h2>
                            <p class="text-muted mb-0">
                                დაიწყეთ და დაასრულეთ თქვენი სამუშაოები „ჩემი სამუშაოები“ ჩანართში,
                                ან აირჩიეთ ახალი სამუშაო „ხელმისაწვდომი სამუშაოები“ ჩანართიდან.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="d-grid gap-2">
                        <a href="{{ route('management.dashboard.tasks', ['tab' => 'mine']) }}"
                            class="btn btn-outline-primary">
                            <i class="bi bi-person-workspace me-1"></i>
                            ჩემი სამუშაოები
                        </a>
                        <a href="{{ route('management.dashboard.tasks', ['tab' => 'available']) }}"
                            class="btn btn-outline-secondary d-flex justify-content-center align-items-center gap-2">
                            <i class="bi bi-collection"></i>
                            ხელმისაწვდომი სამუშაოები
                            <span class="badge text-bg-secondary">{{ $availableTasksCount }}</span>
                        </a>
                        <a href="{{ route('management.worker.tasks.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>
                            სამუშაოს შექმნა
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection