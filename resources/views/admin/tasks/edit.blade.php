@extends('layouts.admin.admin-dashboard')
@section('title', 'სამუშაოს რედაქტირება')
@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT" title="სამუშაოს რედაქტირება"
		action="{{ route($resourceName . '.update', $task) }}" :backRoute="$resourceName . '.index'" :hasFileUpload="true">

		<!--  services & branches -->
		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.select name="service_id" :options="$services" label="სერვისი" :selected="old('service_id', $task->service_id)" :required="true" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.select name="branch_id" :options="$branches" :selected="old('branch_id', $task->branch_id)"
					label="სამიზნე ფილიალი" :required="true" />
			</div>
		</div>

		<!-- workers & visibility  -->
		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.checkbox-dropdown label="შემსრულებლები" :items="$users" name="user_ids" labelField="full_name"
					:selected="old('user_ids', $task->users->pluck('id')->toArray())" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']" :selected="old('visibility', $task->visibility)" label="ხილვადობა" />
			</div>
		</div>

		<!-- Recurrence interval & requirements -->
		<div class="row" id="recurrence-group" data-recurrence-group>
			<div class="col-md-6 mb-3" data-recurrence-toggle>
				<x-form.select name="is_recurring" :options="['1' => 'დიახ', '0' => 'არა']" :selected="old('is_recurring', $task->is_recurring ? '1' : '0')" label="განმეორებადი სამუშაო" />
			</div>
			<div class="col-md-6 mb-3" data-recurrence-interval>
				<x-form.input type="number" name="recurrence_interval" label="განმეორების ინტერვალი (დღე)" placeholder="1-31"
					min="1" max="31" step="1" :required="false"
					value="{{ old('recurrence_interval', $task->recurrence_interval) }}" />
			</div>
		</div>

		<!-- Occurrence details -->
		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.select name="requires_document" :options="['1' => 'დიახ', '0' => 'არა']"
					:selected="old('requires_document', $task->taskOccurrences()->latest()->value('requires_document') ? '1' : '0')" label="სჭირდება დოკუმენტი" />
			</div>
		</div>
	</x-admin.crud.form-container>
@endsection

@section('scripts')
	{!! load_script('scripts/task/taskCreate.js') !!}
@endsection