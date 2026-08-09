@php
    $isAdmin = auth()->user()->isAdmin();
    $routePrefix = $isAdmin ? 'employees' : 'management.employees';
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <div>
        <h1 class="h4 mb-1">თანამშრომლები</h1>
        <p class="text-muted mb-0">თქვენთან დაკავშირებული კომპანიებისა და ფილიალების თანამშრომლები</p>
    </div>

    @can('create', \App\Models\Employee::class)
        <a href="{{ route("{$routePrefix}.create") }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>
            თანამშრომლის შექმნა
        </a>
    @endcan
</div>

<form method="GET" action="{{ route("{$routePrefix}.index") }}" class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-6 col-lg-4">
                <label for="employee-search" class="form-label">ძებნა</label>
                <input id="employee-search" type="search" name="search" class="form-control"
                    value="{{ request('search') }}" placeholder="სახელი, გვარი, პოზიცია, კომპანია ან ფილიალი">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>ძებნა
                </button>
            </div>
            @if (request()->filled('search'))
                <div class="col-auto">
                    <a href="{{ route("{$routePrefix}.index") }}" class="btn btn-outline-secondary">გასუფთავება</a>
                </div>
            @endif
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">სახელი და გვარი</th>
                        <th>კომპანია</th>
                        <th>ფილიალები</th>
                        <th>პოზიცია</th>
                        <th>პირადი მონაცემები</th>
                        <th>ხილვადობა</th>
                        <th class="text-end pe-3">მოქმედება</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td class="ps-3 fw-semibold">{{ $employee->name }} {{ $employee->surname }}</td>
                            <td>{{ $employee->company->name }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach ($employee->branches as $branch)
                                        <span class="badge text-bg-light border">{{ $branch->name }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>{{ $employee->position }}</td>
                            <td>
                                @can('viewPersonalDetails', $employee)
                                    <div class="small">
                                        <div><i class="bi bi-telephone me-1"></i>{{ $employee->phone ?: '—' }}</div>
                                        <div><i class="bi bi-envelope me-1"></i>{{ $employee->email ?: '—' }}</div>
                                        <div><i class="bi bi-person-vcard me-1"></i>{{ $employee->id_number ?: '—' }}</div>
                                    </div>
                                @else
                                    <span class="text-muted small">
                                        <i class="bi bi-lock me-1"></i>დამალულია
                                    </span>
                                @endcan
                            </td>
                            <td>
                                <span class="badge {{ $employee->personal_details_visible ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $employee->personal_details_visible ? 'ხილული' : 'დამალული' }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                    <a href="{{ route("{$routePrefix}.show", $employee) }}"
                                        class="btn btn-sm btn-outline-primary">დეტალები</a>
                                    @can('update', $employee)
                                        <a href="{{ route("{$routePrefix}.edit", $employee) }}"
                                            class="btn btn-sm btn-outline-secondary">რედაქტირება</a>
                                    @endcan
                                    @can('delete', $employee)
                                        <form method="POST" action="{{ route("{$routePrefix}.destroy", $employee) }}"
                                            onsubmit="return confirm('ნამდვილად გსურთ თანამშრომლის წაშლა?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">წაშლა</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">თანამშრომლები ვერ მოიძებნა</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if ($employees->hasPages())
    <div class="mt-3">{{ $employees->links('pagination::bootstrap-5') }}</div>
@endif
