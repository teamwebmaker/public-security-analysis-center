@extends('management.master')
@section('title', 'სამუშაოების მონაცემები')


@section('main')
	<!-- search -->
	<x-shared.search-bar heading="ყველა სამუშაო" headingPosition="left" :action="route('management.dashboard.tasks')" />

	@if ($tasks->isNotEmpty())
		<!-- Tasks -->
		<div class="my-3 shadow-sm rounded-3 overflow-hidden ">
			<x-shared.table :items="$tasks" :headers="['#', 'სტატუსი', 'შემსრულებელი', 'ფილიალი', 'სერვისი', 'სამუშაოს დაწყება', 'სამუშაოს დასრულება',]" :rows="$userTableRows" :sortableMap="['სამუშაოს დაწყება' => 'start_date', 'სამუშაოს დასრულება' => 'end_date',]" :tooltipColumns="['branch', 'service']" :actions="false" />
		</div>
	@else
		<x-ui.empty-state-message :resourceName="null" :overlay="false" />
	@endif
@endsection