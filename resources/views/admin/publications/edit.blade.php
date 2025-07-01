@extends('layouts.dashboard')
@section('title', 'რედაქტირება: ' . $publication->title->ka)

@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT" title="პუბლიკაციის რედაქტირება"
		action="{{ route($resourceName . '.update', $publication) }}" :hasFileUpload="true" :backRoute="$resourceName . '.index'">

		<x-slot name="imageHeader">
			@if($publication->image)
				<x-admin.image-header :src="$publication->image" :folder="$resourceName" caption="პუბლიკაციის ძველი სურათი" />
			@endif
		</x-slot>

		<!-- Multilingual Tabs -->
		<div class="mb-4">
			<x-ui.tabs :tabs="[
			['id' => 'ka', 'label' => 'KA'],
			['id' => 'en', 'label' => 'EN'],
		]">
				<!-- Georgian content -->
				<div class="tab-pane fade show active " id="ka-tab-content" role="tabpanel" aria-labelledby="ka-tab">
					<div class="mb-3">
						<x-form.input name="title_ka" label="სათაური" placeholder="შეიყვანეთ სათაური ქართულად"
							value="{{ old('title_ka', $publication->title->ka) }}" />
					</div>
					<div class="mb-1">
						<x-form.textarea name="description_ka" label="აღწერა"
							value="{{ old('description_ka', $publication->description->ka) }}"
							placeholder="შეიყვანეთ აღწერა ქართულად" />
					</div>
				</div>

				<!-- English content -->
				<div class="tab-pane fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab">
					<div class="mb-3">
						<x-form.input name="title_en" label="Title" placeholder="შეიყვანეთ სათაური ინგლისურად"
							value="{{ old('title_en', $publication->title->en) }}" />
					</div>
					<div class="mb-1">
						<x-form.textarea name="description_en" label="Description"
							value="{{ old('description_en', $publication->description->en) }}"
							placeholder="შეიყვანეთ აღწერა ინგლისურად" />
					</div>
				</div>
			</x-ui.tabs>
		</div>

		<!-- Image and PDF -->
		<div class="row mb-4">
			<div class="col-md-6">
				<x-form.input type="file" id="publication-image" name="image" label="სურათი" :required="false" />
				<x-form.image-upload-preview id="publication" />
			</div>
			<div class="col-md-6">
				<x-form.input type="file" name="file" label="pdf დოკუმენტი" :isImage="false" accept="application/pdf"
					:required="false" />
			</div>
		</div>
		<!-- Visibility & Submit -->
		<div class="row">
			<div class="col-md-4 mb-2">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']"
					selected="{{ old('visibility', $publication->visibility) }}" label="ხილვადობა" />
			</div>
		</div>

	</x-admin.crud.form-container>
@endsection

@section('scripts')
	{!! load_script('scripts/publication/publicationEdit.js') !!}
@endsection