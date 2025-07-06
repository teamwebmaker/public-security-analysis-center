@extends('layouts.dashboard')

@section('title', 'პროექტის შექმნა')

@section('main')
	<x-admin.crud.form-container method="POST" title="სერვისის შექმნა" action="{{ route($resourceName . '.store') }}"
		:hasFileUpload="true" :backRoute="$resourceName . '.index'">

		<!-- Language tabs -->
		<div class="mb-4">
			<x-ui.tabs :tabs="[
			['id' => 'ka', 'label' => 'KA'],
			['id' => 'en', 'label' => 'EN'],
		]">
				<!-- Georgian content -->
				<div class="tab-pane fade show active" id="ka-tab-content" role="tabpanel">
					<div class="mb-3">
						<x-form.input name="title_ka" label="სათაური" value="{{ old('title_ka') }}"
							placeholder="შეიყვანეთ სათაური ქართულად" />
					</div>
					<div class="mb-1">
						<x-form.textarea name="description_ka" label="აღწერა" value="{{ old('description_ka') }}"
							placeholder="შეიყვანეთ აღწერა ქართულად" />
					</div>
				</div>

				<!-- English content -->
				<div class="tab-pane fade" id="en-tab-content" role="tabpanel">
					<div class="mb-3">
						<x-form.input name="title_en" label="Title" value="{{ old('title_en') }}"
							placeholder="შეიყვანეთ სათაური ინგლისურად" />
					</div>
					<div class="mb-1">
						<x-form.textarea name="description_en" label="Description" value="{{ old('description_en') }}"
							placeholder="შეიყვანეთ აღწერა ინგლისურად" />
					</div>
				</div>
			</x-ui.tabs>
		</div>

		<!-- Image and category id -->
		<div class="row mb-4">
			<div class="col-md-6">
				<x-form.input type="file" id="image" name="image" label="სურათი" />
				<x-form.image-upload-preview id="image" />
			</div>
			<div class="col-md-6">
				<x-form.select name="service_category_id" id="service_category_id" :options="$serviceCategories"
					label="კატეგორია" />
			</div>
		</div>

		<!-- Sortable and visibility -->
		<div class="row">
			<div class="col-md-6">
				<x-form.input type="number" id="sortable" name="sortable" label="რიგითობა"
					placeholder="უნიკალური რიგი კატეგორიაში" min="1" />

				<!-- Next available sortable value -->
				<div class="mt-2" id="next-sortable-container">
					<span class="text-muted">შემდეგი თავისუფალი რიგი: <span class="fw-bold"
							id="next-sortable-value"></span></span>
				</div>

				<!-- Already used sortable numbers -->
				<div class="mt-2" id="used-sortables-container" style="display: none;">
					<label class="form-label">უკვე გამოყენებული რიგები:</label>
					<div class="d-flex flex-wrap gap-2" id="used-sortables-badges"></div>
				</div>

				<div class="form-text mt-2">
					რიგის ნომერი უნდა იყოს უნიკალური თითოეულ კატეგორიაში
				</div>
			</div>

			<div class="col-md-6">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']" selected="none"
					label="ხილვადობა" />
			</div>
		</div>

	</x-admin.crud.form-container>
@endsection

@section('scripts')
	{!! load_script('scripts/service/serviceCreate.js') !!}
	{!! load_script('scripts/imgUploadInit.js') !!}

	<script>
		// Pass services to JS	
		window.appData = {
			services: @json($services)
		};
	</script>
@endsection