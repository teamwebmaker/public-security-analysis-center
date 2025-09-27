@extends('management.master')
@section('title', 'მონაცემთა პანელი')

@section('sidebar-content')
	<x-management.dashboard.sidebar-menu :items="$sidebarItems" />
@endsection
@section('main')
	<!-- Stats -->
	<div class="row g-3">
		<x-management.stat-card label="ელოდება სამუშაოს დადასტურებას" :count="$pendingTasks->count()"
			icon="bi bi-hourglass-split" iconWrapperClasses=" bg-warning bg-opacity-10 text-warning" />

		<x-management.stat-card label="აქტიური სამუშაოები" :count="$inProgressTasks->count()" icon="bi bi-ui-radios"
			iconWrapperClasses="bg-info bg-opacity-10 text-info" />

		<x-management.stat-card label="დასრულებული სამუშაოები" :count="$completedTasks->count()" icon="bi bi-check-circle"
			iconWrapperClasses="bg-success bg-opacity-10 text-success" />

		<x-management.stat-card label="შეჩერებული სამუშაოები" :count="$onHoldTasks->count()" icon="bi bi-pause-circle"
			iconWrapperClasses="bg-secondary bg-opacity-10 text-secondary" />
	</div>

	@if ($tasks->isNotEmpty())
		<div class="my-4">
			<!-- search -->
			<form method="GET" class="row row-cols-sm-auto justify-content-between align-items-center mb-2 ">
				<div>
					<p class="fw-bold fs-5 m-0 text-center">ყველა სამუშაო</p>
				</div>
				<div class="row row-cols-1 row-cols-sm-auto g-4 m-0 ">
					<div class="col m-sm-0">
						<input name="filter[search]" value="{{ request('filter.search') }}" type="text" class="form-control w-100"
							placeholder="ძიება...">
						@if(request('filter.search'))
							<p class="text-muted m-0 align-self-start pt-2">საძიებო სიტყვა:
								<strong>{{ request('filter.search') }}</strong>
							</p>
						@endif
					</div>
					<div class="row mt-2 mt-sm-0 g-2 ">
						<div class="col m-sm-0">
							<button type="submit" class="btn btn-primary w-100">ძიება</button>
						</div>
						<div class="col m-sm-0 ">
							<a href="{{ route('management.dashboard.tasks') }}" class="btn btn-danger w-100">
								<i class="bi bi-trash-fill"></i>
							</a>
						</div>
					</div>
				</div>
			</form>

			<!-- Tasks Table -->
			<x-shared.table :items="$tasks" :headers="[
					'#',
					'სტატუსი',
					'ფილიალი',
					'სერვისი',
					'სამუშაოს დაწყება',
					'სამუშაოს დასრულება',
					'___',
				]" :rows="$taskTableRows" :sortableMap="[
					'სამუშაოს დაწყება' => 'start_date',
					'სამუშაოს დასრულება' => 'end_date',
				]" :tooltipColumns="['branch', 'service']" :actions="false"
				:customActions="$customActionBtns" />
		</div>

	@else
		<x-ui.empty-state-message :resourceName="null" :overlay="false" />
	@endif
@endsection