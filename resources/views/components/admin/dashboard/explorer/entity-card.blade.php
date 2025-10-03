@props(['id', 'type', 'title', 'subtitle',])

<div id="explorer-entity-card" class="col" data-id="{{ $id }}" data-type="{{ $type }}" data-bs-toggle="modal"
	data-bs-target="#{{ $type }}Modal" style="cursor: pointer;">
	<div class="card h-100 shadow-sm rounded-4 p-3 cursor-pointer">
		<div class="d-flex justify-content-between align-items-start flex-wrap">
			<div>
				<h2 class="fw-bold mb-1 fs-5 ">
					{{ $title }}
				</h2>
				@if (isset($subtitle))
					<p class="text-muted small mb-2">
						{{ $subtitle }}
					</p>
				@endif
			</div>
			<div class="fs-4">
				<i class="bi bi-file-earmark-medical"></i>
			</div>
		</div>
	</div>
</div>