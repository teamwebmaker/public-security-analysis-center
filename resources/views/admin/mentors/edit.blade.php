@extends('layouts.admin.admin-dashboard')
@section('title', 'რედაქტირება: ' . $mentor->full_name)

@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT" action="{{ route($resourceName . '.update', $mentor) }}"
		title="მენტორის რედაქტირება" :hasFileUpload="true" :backRoute="$resourceName . '.index'">

		<x-slot name="imageHeader">
			@if($mentor->image)
				<x-admin.image-header :src="$mentor->image" :folder="$resourceName" caption="პარტნიორის ძველი სურათი" />
			@endif
		</x-slot>


		<!-- mentor name -->
		<div class="mb-3">
			<x-form.input name="full_name" label="სახელი" value="{{ old('full_name', $mentor->full_name) }}"
				placeholder="შეიყვანეთ სახელი" />
		</div>

		<!-- Multilingual description -->
		<div class="mb-3">
			<x-ui.tabs :tabs="[
			['id' => 'ka', 'label' => 'KA'],
			['id' => 'en', 'label' => 'EN'],
		]">
				<!-- Georgian content -->
				<div class="tab-pane fade show active " id="ka-tab-content" role="tabpanel" aria-labelledby="ka-tab">
					<div class="mb-1">
						<x-form.textarea name="description_ka" label="აღწერა"
							value="{{ old('description_ka', $mentor->description->ka ?? '') ?? '' }}"
							placeholder="შეიყვანეთ აღწერა ქართულად" maxlength="250" :required="false" />
					</div>
				</div>
				<!-- English content -->
				<div class="tab-pane fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab">
					<div class="mb-1">
						<x-form.textarea name="description_en" label="Description"
							value="{{ old('description_en', $mentor->description->ka ?? '') ?? '' }}"
							placeholder="შეიყვანეთ აღწერა ინგლისურად" maxlength="250" :required="false" />
					</div>
				</div>
			</x-ui.tabs>
		</div>

		<div class="row mb-3">
			<!-- Image -->
			<div class="col-md-6">
				<x-form.input type="file" id="image" name="image" label="სურათი" :required="false" />
				<x-form.image-upload-preview id="image" />
			</div>

			<!-- Visibility -->
			<div class="col-md-6">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']"
					selected="{{ old('visibility', $mentor->visibility) }}" label="ხილვადობა" />
			</div>
		</div>
		<div class="col-md-5">
			<x-form.checkbox-dropdown label="აირჩიე პროგრამები" :items="$programs" name="program_ids"
				:selected="isset($mentor) ? $mentor->programs->pluck('id') : old('program_ids', [])" labelField="title.ka" />
		</div>
	</x-admin.crud.form-container>

@endsection
@section('scripts')
	{!! load_script('scripts/imgUploadInit.js') !!}
@endsection