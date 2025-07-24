@extends('layouts.admin.admin-dashboard')
@section('title', 'ფილიალის შექმნა')
@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT" title="ფილიალის რედაქტირება"
		action="{{ route($resourceName . '.update', $branch) }}" :backRoute="$resourceName . '.index'">

		<!-- name and address -->
		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.input name="name" label="სახელი" value="{{ old('name', $branch->name) }}"
					placeholder="შეიყვანეთ სახელი" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.input name="address" label="მისამართი" value="{{ old('address', $branch->address) }}"
					placeholder="შეიყვანეთ მისამართი" />
			</div>
		</div>

		<!-- company_id and visibility -->
		<div class="row">
			<div class="col-md-6">
				<x-form.select name="company_id" :options="$companies" selected="{{ old('company_id', $branch->company_id) }}"
					label="მშობელი კომპანია" />
			</div>
			<div class="col-md-6">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']"
					selected="{{ old('visibility', $branch->visibility) }}" label="ხილვადობა" />
			</div>
		</div>

	</x-admin.crud.form-container>
@endsection