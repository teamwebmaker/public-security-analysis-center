@extends('layouts.admin.admin-dashboard')
@section('title', 'ნომრის რედაქტირება')
@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT" title="ნომრის რედაქტირება"
		action="{{ route($resourceName . '.update', $admin_number) }}" :backRoute="$resourceName . '.index'">

		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.input name="name" label="სახელი"
					value="{{ old('name', $admin_number->name) }}" placeholder="შეიყვანეთ სახელი" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.input name="phone" label="ნომერი"
					value="{{ old('phone', $admin_number->phone) }}" placeholder="შეიყვანეთ ნომერი" />
			</div>
		</div>
	</x-admin.crud.form-container>
@endsection
