@extends('layouts.admin.admin-dashboard')
@section('title', 'სამუშაოს რეგისტრაცია')
@section('main')
	<x-admin.crud.form-container method="POST" title="სამუშაოს რეგისტრაცია" action="{{ route($resourceName . '.store') }}"
		:backRoute="$resourceName . '.index'" :hasFileUpload="true">
		<!--  services & tatuses -->
		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.select name="service_id" :options="$services" label="სერვისი" selected="{{ old('service_id') }}" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.select name="status_id" :options="$statuses" selected="1" label="სტატუსი" />
			</div>
		</div>

		<!-- branches & visibility  -->
		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.select name="branch_id" :options="$branches" selected="{{ old('branch_id') }}"
					label="სამიზნე ფილიალი" />
			</div>

			<div class="col-md-6 ">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']" selected="1"
					label="ხილვადობა" />
			</div>
		</div>
		<!-- document & worker users -->
		<div class="row">
			<div class="col-md-6">
				<x-form.checkbox-dropdown label="შემსრულებლები" :items="$users" name="user_ids" labelField="full_name"
					:selected="old('user_ids')" />
			</div>
			<div class="col-md-6">
				<x-form.input type="file" name="document" label="დოკუმენტი" :isImage="false"
					accept=".pdf, .doc, .docx, .xls, .xlsx" infoMessage="მხარდაჭერილი ფორმატები: .pdf, .doc, .docx, .xls, .xlsx"
					:required="false" />
			</div>
		</div>
	</x-admin.crud.form-container>
@endsection