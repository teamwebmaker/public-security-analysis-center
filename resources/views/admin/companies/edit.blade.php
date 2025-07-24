@extends('layouts.admin.admin-dashboard')
@section('title', 'პროექტის შექმნა')
@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT" title="პროექტის რედაქტირება"
		action="{{ route($resourceName . '.update', $company) }}" :backRoute="$resourceName . '.index'">

		<!-- name and identification_code -->
		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.input name="name" label="სახელი" value="{{ old('name', $company->name) }}"
					placeholder="შეიყვანეთ სახელი" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.input name="identification_code" label="საიდენთიფიკაციო კოდი"
					value="{{ old('identification_code', $company->identification_code) }}"
					placeholder="შეიყვანეთ საიდენთიფიკაციო კოდი" />
			</div>
		</div>

		<!-- economic_activity_type_id and visibility -->

		<div class="row">
			<div class="col-md-6">
				<x-form.select name="economic_activity_type_id" :options="$economic_activity_types"
					selected="{{ old('economic_activity_type_id', $company->economic_activity_type_id) }}"
					label="ეკონომიკური საქ ტიპი" />
			</div>
			<div class="col-md-6">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']"
					selected="{{ old('visibility', $company->visibility) }}" label="ხილვადობა" />
			</div>
		</div>

	</x-admin.crud.form-container>
@endsection