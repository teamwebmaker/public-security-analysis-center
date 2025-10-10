@props(['service', 'resourceName'])

<ul class="list-group list-group-flush">

	<li class="list-group-item d-flex justify-content-between bg-transparent flex-wrap align-items-center">
		<span>კატეგორია:</span>
		@if ($service->category)
			<span class="badge bg-primary"> {{ $service->category->name->ka }}</span>
		@else
			<span class="badge bg-warning text-black d-inline-flex align-items-center gap-2 ">
				<i class="bi bi-exclamation-triangle"></i>
				კავშირი არ არის
			</span>

		@endif
	</li>
	@if ($service->category)
		<li class="list-group-item d-flex justify-content-between bg-transparent flex-wrap align-items-center">
			<span>რიგით:</span>
			<span class="badge bg-primary"> {{ $service->sortable }}</span>
		</li>
	@endif
	@if ($service->document)
		@php
			$documentPath = asset('documents/' . $resourceName . '/' . $service->document);
			$extension = strtolower(pathinfo($service->document, PATHINFO_EXTENSION));

			$icons = [
				'pdf' => 'bi-file-earmark-pdf text-danger',
				'doc' => 'bi-file-earmark-word text-primary',
				'docx' => 'bi-file-earmark-word text-primary',
				'xls' => 'bi-file-earmark-excel text-success',
				'xlsx' => 'bi-file-earmark-excel text-success',
			];

			$iconClass = $icons[$extension] ?? 'bi-file-earmark text-secondary';
		 @endphp

		<li class="list-group-item d-flex justify-content-between bg-transparent flex-wrap align-items-center">
			@if ($extension === 'pdf')
				{{-- Open PDF in fancybox viewer --}}
				<a href="{{ $documentPath }}" data-fancybox data-type="pdf" class="btn btn-sm btn-outline-success w-100">
					<i class="bi {{ $iconClass }} me-2"></i> დოკუმენტი (PDF)
				</a>
			@else
				{{-- For Word/Excel, offer download --}}
				<a href="{{ $documentPath }}" class="btn btn-sm btn-outline-primary w-100" target="_blank" download>
					<i class="bi {{ $iconClass }} me-2"></i> {{ strtoupper($extension) }} ფაილი ჩამოტვირთვა
				</a>
			@endif
		</li>
	@endif

</ul>