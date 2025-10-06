@extends('management.master')
@section('title', 'მონაცემთა პანელი')

@section('sidebar-content')
	<x-management.dashboard.sidebar-menu :items="$sidebarItems" />
@endsection
@section('main')
	<!-- Stats -->
	<div class="row g-3 border-bottom pb-4">
		<x-management.stat-card label="ელოდება სამუშაოს დადასტურებას" :count="$pendingTasks->count()"
			icon="bi bi-hourglass-split" iconWrapperClasses=" bg-warning bg-opacity-10 text-warning" />

		<x-management.stat-card label="აქტიური სამუშაოები" :count="$inProgressTasks->count()" icon="bi bi-ui-radios"
			iconWrapperClasses="bg-info bg-opacity-10 text-info" />

		<x-management.stat-card label="დასრულებული სამუშაოები" :count="$completedTasks->count()" icon="bi bi-check-circle"
			iconWrapperClasses="bg-success bg-opacity-10 text-success" />

		<x-management.stat-card label="შეჩერებული სამუშაოები" :count="$onHoldTasks->count()" icon="bi bi-pause-circle"
			iconWrapperClasses="bg-secondary bg-opacity-10 text-secondary" />
	</div>

	<div class="my-4">
		<!-- search -->
		<x-shared.search-bar heading="ყველა სამუშაო" headingPosition="left" :action="route('management.dashboard.page')" />

		@if ($tasks->isNotEmpty())
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