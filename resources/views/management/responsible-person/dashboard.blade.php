@extends('management.master')
@section('title', 'ფილიალის მონაცემები')

@section('main')
	<!-- Stats -->
	<div class="row g-3">
		<x-management.stat-card label="აქტიური სამუშაოები" :count="$inProgressCount" icon="bi bi-ui-radios"
			iconWrapperClasses="bg-warning bg-opacity-10 text-warning" />

		<x-management.stat-card label="გადასახდელი სამუშაოები" :count="$paymentOccurrences->total()" icon="bi bi-cash"
			iconWrapperClasses="bg-danger bg-opacity-10 text-danger" />

		<x-management.stat-card label="ფილიალები" :count="$userBranches->total()" icon="bi bi-diagram-3-fill"
			iconWrapperClasses="bg-success bg-opacity-10 text-success" />
	</div>

	<!-- Branches -->
	@if ($userBranches->count() > 0)
		<div class="my-4">
			<p class="fw-bold fs-5">ფილიალები</p>
			<div class="my-3 shadow-sm rounded-3 overflow-hidden ">
				<x-shared.table :items="$userBranches" :headers="$branchTableHeaders" :rows="$branchTableRows"
					:tooltipColumns="['name', 'address', 'company']" :actions="false" />
			</div>
			<div class="mt-2">
				{!! $userBranches->withQueryString()->links('pagination::bootstrap-5') !!}
			</div>
		</div>
	@endif

	<!-- Payments -->
	@if ($paymentOccurrences->count() > 0)
		<div class="my-4">
			<p class="fw-bold fs-5">გადასახდელი სამუშაოები</p>
			<div class="my-3 shadow-sm rounded-3 overflow-hidden">
				<x-shared.table :items="$paymentOccurrences" :headers="$paymentHeaders" :rows="$paymentRows"
					:tooltipColumns="['branch', 'service']" :actions="false" />
			</div>
			<div class="mt-2">
				{!! $paymentOccurrences->withQueryString()->links('pagination::bootstrap-5') !!}
			</div>
		</div>
	@endif

	<!-- Empty state -->
	@if ($userBranches->count() === 0 && $paymentOccurrences->count() === 0)
		<x-ui.empty-state-message :resourceName="null" :overlay="false" />
	@endif

@endsection