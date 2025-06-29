@extends('layouts.dashboard')
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
			<x-tabs :tabs="[
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
			</x-tabs>
		</div>



		<div class="row">
			<!-- Image -->
			<div class="col-md-6">
				<x-form.input type="file" id="mentor-image" name="image" label="სურათი" :required="false" />
				<x-form.image-upload-preview id="mentor" />
			</div>

			<!-- Visibility -->
			<div class="col-md-6">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']"
					selected="{{ old('visibility', $mentor->visibility) }}" label="ხილვადობა" />
			</div>
		</div>
	</x-admin.crud.form-container>

@endsection
@section('scripts')
	{!! load_script('scripts/mentor.js') !!}
@endsection