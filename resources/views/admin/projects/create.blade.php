@extends('layouts.dashboard')
@section('title', 'პროექტის შექმნა')
@section('main')
	<x-admin.crud.form-container method="POST" title="პროექტის შექმნა" action="{{ route('projects.store') }}"
		:hasFileUpload="true" backRoute="projects.index">

		<!-- Language tabs -->
		<div class="mb-4">
			<x-tabs :tabs="[
			['id' => 'ka', 'label' => 'KA'],
			['id' => 'en', 'label' => 'EN'],
		]">
				<!-- Georgian content -->
				<div class="tab-pane fade show active " id="ka-tab-content" role="tabpanel" aria-labelledby="ka-tab">
					<div class="mb-3">
						<x-form.input name="title_ka" label="სათაური" value="{{ old('title_ka') }}"
							placeholder="შეიყვანეთ სათაური ქართულად" />
					</div>
					<div class="mb-3">
						<x-form.textarea name="description_ka" label="აღწერა" value="{{ old('description_ka') }}"
							placeholder="შეიყვანეთ აღწერა ქართულად" />
					</div>
				</div>

				<!-- English content -->
				<div class="tab-pane fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab">
					<div class="mb-3">
						<x-form.input name="title_en" label="Title" value="{{ old('title_en') }}"
							placeholder="შეიყვანეთ სათაური ინგლისურად" />
					</div>
					<div class="mb-3">
						<x-form.textarea name="description_en" label="Description" value="{{ old('description_en') }}"
							placeholder="შეიყვანეთ აღწერა ინგლისურად" />
					</div>
				</div>
			</x-tabs>
		</div>

		<!-- Image and visibility -->
		<div class="row mb-4">
			<div class="col-md-6">
				<x-form.input type="file" id="project-image" name="image" label="სურათი" required />
				<x-form.image-upload-preview id="project" />
			</div>
			<div class="col-md-6">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']" selected="1" label="ხილვადობა"
					required />
			</div>
		</div>

	</x-admin.crud.form-container>
@endsection

@section('scripts')
	{!! load_script('scripts/project/projectCreate.js') !!}
@endsection