@extends('layouts.admin.admin-dashboard')
@section('title', 'ეკონომიკური ტიპის რედაქტირება')
@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT" title="ეკონომიკური ტიპის რედაქტირება"
		action="{{ route($resourceName . '.update', $economic_activity_type) }}" :backRoute="$resourceName . '.index'">
		<!-- name and display_name -->
		<div class="row">
			<div class="col">
				<x-form.input name="display_name" label="სახელი (ქარ) "
					value="{{ old('display_name', $economic_activity_type->display_name) }}" />
			</div>
			<div class="col">
				<x-form.input name="name" label="სახელი (ინგლისური) "
					value="{{ old('name', $economic_activity_type->name) }}" />
			</div>
		</div>
	</x-admin.crud.form-container>
@endsection