@php
    $isAdmin = auth()->user()->isAdmin();
    $routePrefix = $isAdmin ? 'employees' : 'management.employees';
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <a href="{{ route("{$routePrefix}.index") }}" class="text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>თანამშრომლები
        </a>
        <h1 class="h4 mt-2 mb-1">{{ $employee->name }} {{ $employee->surname }}</h1>
        <div class="text-muted">{{ $employee->position }}</div>
    </div>

    <div class="d-flex flex-wrap gap-2">
        @can('update', $employee)
            <a href="{{ route("{$routePrefix}.edit", $employee) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i>რედაქტირება
            </a>
        @endcan
        @can('delete', $employee)
            <form method="POST" action="{{ route("{$routePrefix}.destroy", $employee) }}"
                onsubmit="return confirm('ნამდვილად გსურთ თანამშრომლის წაშლა?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">წაშლა</button>
            </form>
        @endcan
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">კომპანია</div>
                <div class="fw-semibold">{{ $employee->company->name }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">შემქმნელი</div>
                <div class="fw-semibold">{{ $employee->creator?->full_name ?? 'წაშლილი მომხმარებელი' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">პირადი მონაცემების ხილვადობა</div>
                <span class="badge {{ $employee->personal_details_visible ? 'text-bg-success' : 'text-bg-secondary' }}">
                    {{ $employee->personal_details_visible ? 'ხილული' : 'დამალული' }}
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent fw-semibold">სამუშაო ინფორმაცია</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">სახელი</dt>
                    <dd class="col-sm-8">{{ $employee->name }}</dd>
                    <dt class="col-sm-4">გვარი</dt>
                    <dd class="col-sm-8">{{ $employee->surname }}</dd>
                    <dt class="col-sm-4">პოზიცია</dt>
                    <dd class="col-sm-8">{{ $employee->position }}</dd>
                    <dt class="col-sm-4">ფილიალები</dt>
                    <dd class="col-sm-8 mb-0">
                        <div class="d-flex flex-wrap gap-1">
                            @foreach ($employee->branches as $branch)
                                <span class="badge text-bg-light border">{{ $branch->name }}</span>
                            @endforeach
                        </div>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent fw-semibold">პირადი მონაცემები</div>
            <div class="card-body">
                @can('viewPersonalDetails', $employee)
                    <dl class="row mb-0">
                        <dt class="col-sm-4">ტელეფონი</dt>
                        <dd class="col-sm-8">{{ $employee->phone ?: 'არ არის მითითებული' }}</dd>
                        <dt class="col-sm-4">ელფოსტა</dt>
                        <dd class="col-sm-8">{{ $employee->email ?: 'არ არის მითითებული' }}</dd>
                        <dt class="col-sm-4">პირადი ნომერი</dt>
                        <dd class="col-sm-8 mb-0">{{ $employee->id_number ?: 'არ არის მითითებული' }}</dd>
                    </dl>
                @else
                    <div class="text-muted">
                        <i class="bi bi-lock me-1"></i>
                        პირადი მონაცემები დამალულია. მათი ნახვა მხოლოდ შემქმნელსა და ადმინისტრატორს შეუძლია.
                    </div>
                @endcan
            </div>
        </div>
    </div>
</div>
