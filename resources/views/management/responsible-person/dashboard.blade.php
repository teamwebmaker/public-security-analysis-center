@extends('management.master')
@section('title', 'ფილიალის მონაცემები')

@section('main')
	<!-- Stats -->
	<div class="row g-3">
		<x-management.stat-card label="აქტიური სამუშაოები" :count="$inProgressTasks->total()" icon="bi bi-ui-radios"
			iconWrapperClasses="bg-warning bg-opacity-10 text-warning" />

		<x-management.stat-card label="ფილიალები" :count="$userBranches->total()" icon="bi bi-diagram-3-fill"
			iconWrapperClasses="bg-success bg-opacity-10 text-success" />
	</div>

	<!-- Tasks -->
	@if ($inProgressTasks->count() > 0)
		<div class="my-4">
			<p class="fw-bold fs-5">აქტიური სამუშაოები</p>
			<div class="my-3 shadow-sm rounded-3 overflow-hidden">
				<x-shared.table :items="$inProgressTasks" :headers="$taskHeaders" :rows="$taskRows" :sortableMap="$sortableMap"
					:tooltipColumns="['branch', 'service']" :actions="false" />
			</div>
			<div class="mt-2">
				{!! $inProgressTasks->withQueryString()->links('pagination::bootstrap-5') !!}
			</div>
		</div>
	@endif

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

	<!-- Empty state -->
	@if ($inProgressTasks->count() === 0 && $userBranches->count() === 0) <x-ui.empty-state-message :resourceName="null"
	:overlay="false" /> @endif

@endsection
