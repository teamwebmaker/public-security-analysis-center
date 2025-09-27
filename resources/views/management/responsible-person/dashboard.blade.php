@extends('management.master')
@section('title', 'ფილიალის მონაცემები')

@section('main')
	<!-- Stats -->
	<div class="row g-3">
		<x-management.stat-card label="აქტიური სამუშაოები" :count="$inProgressTasks->count()" icon="bi bi-ui-radios"
			iconWrapperClasses="bg-warning bg-opacity-10 text-warning" />

		<x-management.stat-card label="ფილიალები" :count="$userBranches->count()" icon="bi bi-diagram-3-fill"
			iconWrapperClasses="bg-success bg-opacity-10 text-success" />
	</div>

	<!-- Tasks -->
	@if ($inProgressTasks->isNotEmpty())
		<div class="my-4">
			<p class="fw-bold fs-5">აქტიური სამუშაოები</p>
			<div class="my-3 shadow-sm rounded-3 overflow-hidden ">
				<x-shared.table :items="$inProgressTasks" :headers="['#', 'სტატუსი', 'შემსრულებელი', 'ფილიალი', 'სერვისი', 'სამუშაოს დაწყება',]" :rows="$userTableRowsWithoutEndDate" :sortableMap="['სამუშაოს დაწყება' => 'start_date',]"
					:tooltipColumns="['branch', 'service']" :actions="false" />
			</div>
		</div>

	@endif

	<!-- Branches -->
	@if ($userBranches->isNotEmpty())
		<div class="my-4">
			<p class="fw-bold fs-5">ფილიალები</p>
			<div class="my-3 shadow-sm rounded-3 overflow-hidden ">
				<x-shared.table :items="$userBranches" :headers="['#', 'სახელი', 'მისამართი', 'მშობელი კომპანია', 'შექმნის თარიღი']" :rows="$branchTableRows" :sortableMap="['შექმნის თარიღი' => 'created_at']" :tooltipColumns="['name', 'address', 'company']" :actions="false" />
			</div>
		</div>
	@endif

	<!-- Empty state -->
	@if ($inProgressTasks->isEmpty() && $userBranches->isEmpty()) <x-ui.empty-state-message :resourceName="null"
	:overlay="false" /> @endif

@endsection