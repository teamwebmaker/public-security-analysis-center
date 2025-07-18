@extends('layouts.admin.admin-dashboard')
@section('title', 'რედაქტირება: ' . $project->title->ka)

@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT" action="{{ route($resourceName . '.update', $project) }}"
		title="პროექტის რედაქტირება" :hasFileUpload="true" :backRoute="$resourceName . '.index'">

		<x-slot name="imageHeader">
			@if ($project->image)
				<x-admin.image-header :src="$project->image" :folder="$resourceName" caption="პროექტის ძველი სურათი" />
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
							value="{{ old('title_ka', $project->title->ka) }}" />
					</div>
					<div class="mb-1">
						<x-form.textarea name="description_ka" label="აღწერა"
							value="{{ old('description_ka', $project->description->ka) }}"
							placeholder="შეიყვანეთ აღწერა ქართულად" />
					</div>
				</div>

				<!-- English content -->
				<div class="tab-pane fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab">
					<div class="mb-3">
						<x-form.input name="title_en" label="Title" placeholder="შეიყვანეთ სათაური ინგლისურად"
							value="{{ old('title_en', $project->title->en) }}" />
					</div>
					<div class="mb-1">
						<x-form.textarea name="description_en" label="Description"
							value="{{ old('description_en', $project->description->en) }}"
							placeholder="შეიყვანეთ აღწერა ინგლისურად" />
					</div>
				</div>

			</x-ui.tabs>
		</div>

		<div class="row">
			<!-- Image -->
			<div class="col-md-6">
				<x-form.input type="file" id="image" name="image" label="სურათი" :required="false" />
				<x-form.image-upload-preview id="image" />
			</div>
			<!-- visibility -->
			<div class="col-md-6">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']"
					selected="{{ old('visibility', $project->visibility) }}" label="ხილვადობა" />
			</div>
		</div>
	</x-admin.crud.form-container>
@endsection

@section('scripts')
	{!! load_script('scripts/imgUploadInit.js') !!}
@endsection