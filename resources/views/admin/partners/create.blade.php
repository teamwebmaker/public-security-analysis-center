@extends('layouts.dashboard')
@section('title', 'პარტნიორის შექმნა')
@section('main')

	<x-admin.crud.form-container title="პარტნიორის შექმნა" action="{{ route('partners.store') }}" method="POST"
		:hasFileUpload="true" backRoute="partners.index">
		<!-- partner title -->
		<div class="mb-3">
			<x-form.input name="title" label="სათაური" placeholder="შეიყვანეთ სათაური" />
		</div>

		<!-- partner link -->
		<div class="mb-3">
			<x-form.input name="link" label="ლინკი" type="url" icon="link-45deg" placeholder="პარტნიორის ლინკი" />
		</div>

		<div class="row mb-3">
			<!-- Image -->
			<div class="col-md-6">
				<x-form.input type="file" id="partner-image" name="image" label="სურათი" />
				<x-form.image-upload-preview id="partner" />
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
	{!! load_script('scripts/partner.js') !!}
@endsection