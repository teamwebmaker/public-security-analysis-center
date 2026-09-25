@props(['id', 'type', 'title', 'subtitle', 'role' => null, 'active' => true, 'summaryUrl' => null])

@php
    $roleIcons = [
        'worker' => 'bi-person-gear',
        'company_leader' => 'bi-building-gear',
        'responsible_person' => 'bi-person-check',
    ];
@endphp

<div class="col">
	<button type="button" class="card h-100 w-100 border-0 text-start shadow-sm rounded-4 p-3"
		data-id="{{ $id }}" data-type="{{ $type }}" @if($summaryUrl) data-summary-url="{{ $summaryUrl }}" @endif
		data-bs-toggle="modal" data-bs-target="#{{ $type }}Modal">
		<div class="d-flex justify-content-between align-items-start flex-wrap">
			<div>
				<h2 class="fw-bold mb-1 fs-5">
					{{ $title }}
				</h2>
				@if (isset($subtitle))
					<p class="text-muted small mb-2">
						{{ $subtitle }}
					</p>
				@endif
			</div>
			<div class="text-end">
				<i class="bi {{ $roleIcons[$role] ?? 'bi-person' }} fs-4 text-primary"></i>
				<div class="mt-2">
					<span class="badge {{ $active ? 'text-bg-success' : 'text-bg-secondary' }}">
						{{ $active ? 'აქტიური' : 'არააქტიური' }}
					</span>
				</div>
			</div>
		</div>
	</button>
</div>
