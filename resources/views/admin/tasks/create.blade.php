@extends('layouts.admin.admin-dashboard')
@section('title', 'სამუშაოს რეგისტრაცია')
@section('main')
	<x-admin.crud.form-container method="POST" title="სამუშაოს რეგისტრაცია" action="{{ route($resourceName . '.store') }}"
		:backRoute="$resourceName . '.index'">
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
			<div class="col-md-6 mb-3">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']" selected="1"
					label="ხილვადობა" />
			</div>
		</div>
		<div class="row">

			<!-- start_date & end_date disabled -->
			{{-- <div class="col-md-6 mb-3">
				<x-form.input type="datetime-local" name="start_date" label="საწყისი თარიღი" value="{{ old('start_date') }}"
					min="{{ date('Y-m-d') }}" />

			</div>
			<div class="col-md-6 mb-3">
				<x-form.input type="datetime-local" name="end_date" label="დასასრული თარიღი" value="{{ old('end_date') }}"
					min="{{ date('Y-m-d') }}" :required="false" />
			</div> --}}

			<!-- worker users -->
			<div class="col-md-6">
				<x-form.checkbox-dropdown label="შემსრულებლები" :items="$users" name="user_ids" labelField="full_name"
					:selected="old('user_ids')" />
			</div>
		</div>
	</x-admin.crud.form-container>
@endsection