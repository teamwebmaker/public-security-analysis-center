@extends('layouts.admin.admin-dashboard')

@section('title', 'სამუშაოების სია')

<!-- Display Tasks -->
<x-admin.index-view :items="$tasks" :resourceName="$resourceName" containerClass="position-relative">
	<!-- Filters & Search -->
	<div
		class="d-flex flex-column align-items-start flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3 border-bottom">
		<div>
			<x-shared.filter-bar :filters="$filters" :showBadges="false" :resetUrl="route($resourceName . '.index')" />
		</div>
		<div class="ms-lg-auto">
			<x-shared.search-bar headingPosition="left" :action="route($resourceName . '.index')" formClass="mb-0"
				:minLength="1" />
		</div>
	</div>

	<x-shared.result-count :count="$tasks->total()" />

	<!-- Tasks -->
	@if (!$tasks->isEmpty())
		<x-shared.table :items="$tasks" :headers="$taskHeaders" :rows="$taskRows" :actions="true" :tooltipColumns="['branch', 'service']" :sortableMap="$sortableMap" :resourceName="$resourceName" :modalTriggers="$occurrenceModalTriggers"
			deleteMessage="ნამდვილად გსურთ სამუშაოს წაშლა? ამ მოქმედებით ასევე წაიშლება მასთან დაკავშირებული ციკლები." />
	@endif
</x-admin.index-view>

<!-- Display Task Occurrences -->
@if(!$tasks->isEmpty())
	@foreach($tasks as $task)
		<x-modal :id="'task-occurrences-' . $task->id" :title="'ციკლები — სამუშაო #' . $task->id" size="xl"
			:data-occurrences-url="route('tasks.occurrences', $task)">
			<div class="d-flex justify-content-end px-3 pt-3">
				<a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-primary">სამუშაოს რედაქტირება</a>
			</div>
			<div class="p-3 js-occurrences-body">
				<div class="js-occurrences-loader d-flex justify-content-center align-items-center h-75 py-4">
					<div class="spinner-border text-primary" role="status">
						<span class="visually-hidden">Loading...</span>
					</div>
				</div>
				<div class="js-occurrences-content"></div>
			</div>
		</x-modal>
	@endforeach
@endif
@section('scripts')
	{!! load_script('scripts/task/taskIndex.js') !!}
@endsection
