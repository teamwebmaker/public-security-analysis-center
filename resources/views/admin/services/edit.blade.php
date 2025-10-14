@extends('layouts.admin.admin-dashboard')
@section('title', 'რედაქტირება: ' . $service->title->ka)

@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT" title="სერვისის რედაქტირება"
		action="{{ route($resourceName . '.update', $service) }}" :hasFileUpload="true" :backRoute="$resourceName . '.index'">

		<x-slot name="imageHeader">
			@if($service->image)
				<x-admin.image-header :src="$service->image" :folder="$resourceName" caption="სერვისის ძველი სურათი" />
			@endif
		</x-slot>

		<!-- Language tabs -->
		<div class="mb-4">
			<x-ui.tabs :tabs="[
			['id' => 'ka', 'label' => 'KA'],
			['id' => 'en', 'label' => 'EN'],
		]">
				<!-- Georgian content -->
				<div class="tab-pane fade show active " id="ka-tab-content" role="tabpanel" aria-labelledby="ka-tab">
					<div class="mb-3">
						<x-form.input name="title_ka" label="სათაური" value="{{ old('title_ka', $service->title->ka) }}"
							placeholder="შეიყვანეთ სათაური ქართულად" />
					</div>
					<div class="mb-1">
						<x-form.textarea name="description_ka" label="აღწერა"
							value="{{ old('description_ka', $service->description->ka) }}"
							placeholder="შეიყვანეთ აღწერა ქართულად" />
					</div>
				</div>

				<!-- English content -->
				<div class="tab-pane fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab">
					<div class="mb-3">
						<x-form.input name="title_en" label="Title" value="{{ old('title_en', $service->title->en) }}"
							placeholder="შეიყვანეთ სათაური ინგლისურად" />
					</div>
					<div class="mb-1">
						<x-form.textarea name="description_en" label="Description"
							value="{{ old('description_en', $service->description->en) }}"
							placeholder="შეიყვანეთ აღწერა ინგლისურად" />
					</div>
				</div>
			</x-ui.tabs>
		</div>

		<!-- Image and document -->
		<div class="row mb-4">
			<div class="col-md-6">
				<x-form.input type="file" id="image" name="image" label="სურათი" :required="false" />
				<x-form.image-upload-preview id="image" />
			</div>

			<div class="col-md-6">
				<x-form.input type="file" name="document" label="დოკუმენტი" :isImage="false"
					accept=".pdf, .doc, .docx, .xls, .xlsx" infoMessage="მხარდაჭერილი ფორმატები: .pdf, .doc, .docx, .xls, .xlsx"
					:required="false" />
			</div>
		</div>

		<!-- category and visibility -->
		<div class="row">
			<div class="col-md-6">
				<!-- One that causes errors  can't get value from it :/-->
				{{-- <x-form.select name="service_category_id" :options="$serviceCategories" label="კატეგორია"
					:selected="old('service_category_id', $service->service_category_id ?? null)" :required="false" /> --}}
				<!-- One that works fine temp usage-->
				<x-admin.services.select name="service_category_id" id="service_category_id" :options="$serviceCategories"
					:selected="old('service_category_id', $service->service_category_id ?? null)" label="კატეგორია" />
			</div>

			<div class="col-md-6">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']"
					selected="{{ old('visibility', $service->visibility) }}" label="ხილვადობა" />
			</div>
		</div>

		<!-- Sortable (order) -->
		<div class="col-md-5 mt-4">
			<x-form.input type="number" id="sortable" name="sortable" value="{{ old('sortable', $service->sortable) }}"
				label="რიგითობა" placeholder="უნიკალური რიგი კატეგორიაში " min="1" />

			<!-- Already used sortable numbers -->
			<div class="mt-2" id="used-sortables-container" style="display: none;">
				<label class="form-label">უკვე გამოყენებული რიგები:</label>
				<div class="d-flex flex-wrap gap-2" id="used-sortables-badges"></div>
			</div>
			<!-- disclaimer -->
			<div class="form-text">
				რიგის ნომერი უნდა იყოს უნიკალური თითოეულ კატეგორიაში
			</div>
		</div>


	</x-admin.crud.form-container>
@endsection

@section('scripts')
	{!! load_script('scripts/service/serviceEdit.js') !!}
	{!! load_script('scripts/imgUploadInit.js') !!}

	<script>
		// Pass services to JS	
		window.appData = {
			services: @json($services),
			currentService: @json($service)
		};

	</script>
@endsection