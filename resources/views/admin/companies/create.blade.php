@extends('layouts.admin.admin-dashboard')
@section('title', 'კომპანიის შექმნა')
@section('main')
	<x-admin.crud.form-container method="POST" title="კომპანიის შექმნა" action="{{ route($resourceName . '.store') }}"
		:backRoute="$resourceName . '.index'">

		<!-- name and identification_code -->
		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.input name="name" label="სახელი" value="{{ old('name') }}" placeholder="შეიყვანეთ სახელი" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.input name="identification_code" label="საიდენთიფიკაციო კოდი" value="{{ old('identification_code') }}"
					placeholder="შეიყვანეთ საიდენთიფიკაციო კოდი" />
			</div>
		</div>

		<!-- economic_activity_type_id and visibility -->
		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.select name="economic_activity_type_id" :options="$economic_activity_types"
					value="{{ old('economic_activity_type_id') }}" label="ეკონომიკური საქ ტიპი" :required="false" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']" selected="1"
					label="ხილვადობა" />
			</div>
		</div>

		<div class="col-md-5">
			<x-form.checkbox-dropdown label="შემსრულებლები" :items="$users" name="user_ids" labelField="full_name"
				:selected="old('user_ids')" />
		</div>

	</x-admin.crud.form-container>
@endsection