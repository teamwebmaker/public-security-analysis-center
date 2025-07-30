@extends('layouts.admin.admin-dashboard')
@section('title', 'მენტორის შექმნა')
@section('main')

	<x-admin.crud.form-container title="მენტორის შექმნა" action="{{ route($resourceName . '.store') }}" method="POST"
		:hasFileUpload="true" :backRoute="$resourceName . '.index'">

		<!-- mentor name -->
		<div class="mb-3">
			<x-form.input name="full_name" label="სახელი" placeholder="შეიყვანეთ სახელი" />
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
						<x-form.textarea name="description_ka" label="აღწერა" value="{{ old('description_ka') }}"
							placeholder="შეიყვანეთ აღწერა ქართულად" maxlength="250" :required="false" />
					</div>
				</div>
				<!-- English content -->
				<div class="tab-pane fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab">
					<div class="mb-1">
						<x-form.textarea name="description_en" label="Description" value="{{ old('description_en') }}"
							placeholder="შეიყვანეთ აღწერა ინგლისურად" maxlength="250" :required="false" />
					</div>
				</div>
			</x-ui.tabs>
		</div>

		<div class="row mb-3">
			<!-- Image -->
			<div class="col-md-6">
				<x-form.input type="file" id="image" name="image" label="სურათი" />
				<x-form.image-upload-preview id="image" />
			</div>

			<div class="col-md-6">
				<!-- Visibility -->
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']" selected="1"
					label="ხილვადობა" />
			</div>
		</div>

		<div class="col-md-5">
			<x-form.checkbox-dropdown label="პროგრამები" :items="$programs" name="program_ids" labelField="title.ka" />
		</div>

	</x-admin.crud.form-container>

@endsection
@section('scripts')
	{!! load_script('scripts/imgUploadInit.js') !!}
@endsection