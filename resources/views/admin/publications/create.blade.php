@extends('layouts.dashboard')
@section('title', 'პიბლიკაციის შექმნა')
@section('main')
	<x-admin.crud.form-container method="POST" title="პიბლიკაციის შექმნა" action="{{ route('publications.store') }}"
		:hasFileUpload="true" backRoute="publications.index">

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

		<!-- Image and PDF -->
		<div class="row mb-4">
			<div class="col-md-6">
				<x-form.input type="file" id="publication-image" name="image" label="სურათი" />
				<x-form.image-upload-preview id="publication" />
			</div>
			<div class="col-md-6">
				<x-form.input type="file" name="file" label="pdf დოკუმენტი" :isImage="false" accept="application/pdf" />
			</div>
		</div>
		<!-- Visibility & Submit -->
		<div class="row">
			<div class="col-md-4 mb-2">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']" selected="1"
					label="ხილვადობა" />
			</div>
		</div>

	</x-admin.crud.form-container>
@endsection

@section('scripts')
	{!! load_script('scripts/publication/publicationCreate.js') !!}
@endsection