@props([
    'action',
    'searchPlaceholder' => 'ძიება',
    'filters' => [],
])

@php
    $activeFilterKeys = collect(array_keys($filters))->filter(fn ($key) => request()->filled($key));
    $hasActiveValues = request()->filled('search') || $activeFilterKeys->isNotEmpty();
    $fieldIdPrefix = 'resource-filter-' . substr(md5($action), 0, 8);
@endphp

<form method="GET" action="{{ $action }}" class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-6 col-xl-4">
                <label for="{{ $fieldIdPrefix }}-search" class="form-label">ძებნა</label>
                <input id="{{ $fieldIdPrefix }}-search" type="search" name="search" class="form-control"
                    value="{{ request('search') }}" placeholder="{{ $searchPlaceholder }}">
            </div>

            @foreach ($filters as $key => $filter)
                <div class="col-md-6 col-xl-2">
                    <label for="{{ $fieldIdPrefix }}-{{ $key }}" class="form-label">{{ $filter['label'] }}</label>
                    <select id="{{ $fieldIdPrefix }}-{{ $key }}" name="{{ $key }}" class="form-select">
                        <option value="">{{ $filter['placeholder'] ?? 'ყველა' }}</option>
                        @foreach ($filter['options'] as $value => $label)
                            <option value="{{ $value }}" @selected((string) request($key) === (string) $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endforeach

            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i>გამოყენება
                </button>
                @if ($hasActiveValues)
                    <a href="{{ $action }}" class="btn btn-outline-secondary">გასუფთავება</a>
                @endif
            </div>
        </div>
    </div>
</form>
