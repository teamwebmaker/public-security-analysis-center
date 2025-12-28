@extends('layouts.admin.admin-dashboard')
@section('title', 'ნომრის შექმნა')
@section('main')
	<x-admin.crud.form-container method="POST" title="ნომრის შექმნა"
		action="{{ route($resourceName . '.store') }}" :backRoute="$resourceName . '.index'">

		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.input name="name" label="სახელი" value="{{ old('name') }}" placeholder="შეიყვანეთ სახელი" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.input name="phone" label="ნომერი" value="{{ old('phone') }}" placeholder="შეიყვანეთ ნომერი" />
			</div>
		</div>
	</x-admin.crud.form-container>
@endsection
