@extends('layouts.admin.admin-dashboard')
@section('title', 'რედაქტირება: ' . $partner->title)

@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT" action="{{ route($resourceName . '.update', $partner) }}"
		title="პარტნიორის რედაქტირება" :hasFileUpload="true" :backRoute="$resourceName . '.index'">

		<x-slot name="imageHeader">
			@if($partner->image)
				<x-admin.image-header :src="$partner->image" :folder="$resourceName" caption="პარტნიორის ძველი სურათი" />
			@endif
		</x-slot>

		<!-- partner title -->
		<div class="mb-3">
			<x-form.input name="title" label="სათაური" value="{{ old('title', $partner->title) }}"
				placeholder="შეიყვანეთ სათაური" />
		</div>

		<!-- partner link -->
		<div class="mb-4">
			<x-form.input type="url" name="link" label="ლინკი" value="{{ old('link', $partner->link) }}"
				placeholder="https://example.com" icon="link-45deg" />
		</div>

		<div class="row">
			<!-- Image -->
			<div class="col-md-6">
				<x-form.input type="file" id="image" name="image" label="სურათი" :required="false" />
				<x-form.image-upload-preview id="image" />
			</div>
			<!-- Visibility -->
			<div class="col-md-6">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']"
					selected="{{ old('visibility', $partner->visibility) }}" label="ხილვადობა" />
			</div>
		</div>
	</x-admin.crud.form-container>
@endsection

@section('scripts')
	{!! load_script('scripts/imgUploadInit.js') !!}
@endsection