@extends('layouts.admin.admin-dashboard')

@section('title', 'სამუშაოების სია')

<!-- Display Tasks -->
<x-admin.index-view :items="$tasks" :resourceName="$resourceName" containerClass="position-relative">
	<!-- Filters & Search -->
	<div class="d-flex flex-column flex-lg-row align-items-center mb-3 border-bottom">
		<div class="flex-fill">
			<x-shared.filter-bar :filters="$filters" :showBadges="false" :resetUrl="route($resourceName . '.index')" />
		</div>
		<div class="flex-fill flex-lg-grow-0">
			<x-shared.search-bar headingPosition="left" :action="route($resourceName . '.index')" formClass="mb-0" />
		</div>
	</div>

	<!-- Tasks -->
	@if (!$tasks->isEmpty())
		<x-shared.table :items="$tasks" :headers="$taskHeaders" :rows="$taskRows" :actions="true" :tooltipColumns="['branch', 'service']" :sortableMap="$sortableMap" :resourceName="$resourceName" :modalTriggers="$occurrenceModalTriggers" deleteMessage="ნამდვილად გსურთ სამუშაოს წაშლა? ამ მოქმედებით ასევე წაიშლება მასთან დაკავშირებული ციკლები." />
	@endif
</x-admin.index-view>

<!-- Display Task Occurrences -->
@if(!$tasks->isEmpty())
	@foreach($tasks as $task)
		<x-modal :id="'task-occurrences-' . $task->id" :title="'ციკლები — სამუშაო #' . $task->id" size="xl">
			<div class="d-flex justify-content-end px-3 pt-3">
				<a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-primary">სამუშაოს რედაქტირება</a>
			</div>
			<div class="p-3">
				@if(($task->taskOccurrences ?? collect())->isEmpty())
					<p class="text-muted mb-0">ციკლები ჯერ არ არის.</p>
				@else
					<x-shared.table :items="$task->taskOccurrences" :headers="$occurrenceHeaders" :rows="$occurrenceRows[$task->id]"
						:tooltipColumns="['branch', 'service']" />
				@endif
			</div>
		</x-modal>
	@endforeach
@endif