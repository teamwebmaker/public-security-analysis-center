@extends('layouts.admin.admin-dashboard')
@section('title', 'ფილიალის შექმნა')
@section('main')
	<x-admin.crud.form-container method="POST" title="ფილიალის შექმნა" action="{{ route($resourceName . '.store') }}"
		:backRoute="$resourceName . '.index'">

		<!-- name and address -->
		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.input name="name" label="სახელი" value="{{ old('name') }}" placeholder="შეიყვანეთ სახელი" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.input name="address" label="მისამართი" value="{{ old('address') }}"
					placeholder="შეიყვანეთ მისამართი" />
			</div>
		</div>

		<!-- company_id and visibility -->
		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.select name="company_id" :options="$companies" label="მშობელი კომპანია" />
			</div>
			<div class="col-md-6 mb-4">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']" selected="1"
					label="ხილვადობა" />
			</div>
		</div>
		<div class="col-md-5">
			<x-form.checkbox-dropdown label="პასუხისმგებელი პირი" :items="$users" name="user_ids" labelField="full_name" />
		</div>
	</x-admin.crud.form-container>
@endsection