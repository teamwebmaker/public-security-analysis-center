@extends('layouts.admin.admin-dashboard')
@section('title', 'პარტნიორის შექმნა')
@section('main')

	<x-admin.crud.form-container title="პარტნიორის შექმნა" action="{{ route($resourceName . '.store') }}" method="POST"
		:hasFileUpload="true" :backRoute="$resourceName . '.index'">

		<!-- partner title -->
		<div class="mb-3">
			<x-form.input name="title" label="სათაური" placeholder="შეიყვანეთ სათაური" value="{{ old('title') }}" />
		</div>

		<!-- partner link -->
		<div class="mb-4">
			<x-form.input name="link" label="ლინკი" type="url" icon="link-45deg" placeholder="პარტნიორის ლინკი"
				value="{{ old('link') }}" />
		</div>

		<div class="row">
			<!-- Image -->
			<div class="col-md-6">
				<x-form.input type="file" id="image" name="image" label="სურათი" />
				<x-form.image-upload-preview id="image" />
			</div>

			<!-- Visibility -->
			<div class="col-md-6">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']" selected="1"
					label="ხილვადობა" />
			</div>
		</div>
	</x-admin.crud.form-container>

@endsection

@section('scripts')
	{!! load_script('scripts/imgUploadInit.js') !!}
@endsection