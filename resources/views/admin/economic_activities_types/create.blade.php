@extends('layouts.admin.admin-dashboard')
@section('title', 'ეკონომიკური ტიპის შექმნა')
@section('main')
	<x-admin.crud.form-container method="POST" title="ეკონომიკური ტიპის შექმნა"
		action="{{ route($resourceName . '.store') }}" :backRoute="$resourceName . '.index'">

		<!-- name and display_name -->
		<div class="row">
			<div class="col">
				<x-form.input name="display_name" label="სახელი (ქარ) " value="{{ old('display_name') }}" />
			</div>
			<div class="col">
				<x-form.input name="name" label="სახელი (ინგლისური) " value="{{ old('name') }}" />
			</div>
		</div>
	</x-admin.crud.form-container>
@endsection