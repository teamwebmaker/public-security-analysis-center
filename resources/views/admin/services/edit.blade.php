@extends('layouts.dashboard')
@section('title', 'რედაქტირება: ' . $service->title->ka)

@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT" title="სერვისის რედაქტირება"
		action="{{ route('services.update', $service) }}" :hasFileUpload="true" backRoute="services.index">

		<!-- Language tabs -->
		<div class="mb-4">
			<x-tabs :tabs="[
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
			</x-tabs>
		</div>

		<!-- Image and visibility -->
		<div class="row mb-4">
			<div class="col-md-6">
				<x-form.input type="file" id="service-image" name="image" label="სურათი" :required="false" />
				<x-form.image-upload-preview id="service" />
			</div>
			<div class="col-md-6">
				<x-form.select name="service_category_id" :options="$serviceCategories" label="კატეგორია"
					:selected="old('service_category_id', $service->service_category_id ?? null)" :required="false" />

			</div>
		</div>

		<div class="row mb-4">

			<div class="col-md-6">
				<x-form.input type="number" id="sortable" name="sortable" value="{{ old('sortable', $service->sortable) }}"
					label="რიგითობა" placeholder="უნიკალური რიგი კატეგორიაში " min="1" />
				<div class="form-text">
					რიგის ნომერი უნდა იყოს უნიკალური თითოეულ კატეგორიაში
				</div>
			</div>
			<div class="col-md-6">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']"
					selected="{{ old('visibility', $service->visibility) }}" label="ხილვადობა" />
			</div>
		</div>

	</x-admin.crud.form-container>
@endsection

@section('scripts')
	{!! load_script('scripts/service/serviceCreate.js') !!}
@endsection