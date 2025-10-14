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
		<li class="list-group-item d-flex justify-content-between bg-transparent flex-wrap align-items-center">
			<x-ui.document-link :file="$service->document" :path="'documents/' . $resourceName" label="დოკუმენტი" />
		</li>
	@endif

</ul>