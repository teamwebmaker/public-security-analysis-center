@extends('layouts.admin.admin-dashboard')
@section('title', 'სამუშაოს რედაქტირება')
@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT" title="სამუშაოს რედაქტირება"
		action="{{ route($resourceName . '.update', $task) }}" :backRoute="$resourceName . '.index'" :hasFileUpload="true">

		<!--  services & tatuses -->
		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.select name="service_id" :options="$services" label="სერვისი" :selected="old('service_id', $task->service_id)" :required="false" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.select name="status_id" :options="$statuses" :selected="old('status_id', $task->status_id)"
					label="სტატუსი" />
			</div>
		</div>

		<!-- branches & visibility  -->
		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.select name="branch_id" :options="$branches" :selected="old('branch_id', $task->branch_id)"
					label="სამიზნე ფილიალი" :required="false" />

			</div>
			<div class="col-md-6 mb-3">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']" :selected="old('visibility', $task->visibility)" label="ხილვადობა" />
			</div>
		</div>

		<!-- worker users -->
		<div class="row">
			<div class="col-md-6">
				<x-form.checkbox-dropdown label="შემსრულებლები" :items="$users" name="user_ids" labelField="full_name"
					:selected="old('user_ids', $task->users->pluck('id')->toArray())" />
			</div>
			<div class="col-md-6">
				<x-form.input type="file" name="document" label="დოკუმენტი" :isImage="false"
					accept=".pdf, .doc, .docx, .xls, .xlsx" infoMessage="მხარდაჭერილი ფორმატები: .pdf, .doc, .docx, .xls, .xlsx"
					:required="false" />
			</div>
		</div>
	</x-admin.crud.form-container>
@endsection