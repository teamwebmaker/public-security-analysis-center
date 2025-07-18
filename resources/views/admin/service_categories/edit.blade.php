@extends('layouts.admin.admin-dashboard')
@section('title', 'რედაქტირება: ' . $serviceCategory->name->ka)

@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT"
		action="{{ route($resourceName . '.update', $serviceCategory) }}" title="სერვის კატეგორიის რედაქტირება"
		:hasFileUpload="false" :backRoute="$resourceName . '.index'" cardWrapperClass="col col-md-8">

		<!-- title ka en -->
		<div class="mb-3">
			<x-ui.tabs :tabs="[
			['id' => 'ka', 'label' => 'KA'],
			['id' => 'en', 'label' => 'EN'],
		]">
				<!-- Georgian content -->
				<div class="tab-pane fade show active" id="ka-tab-content" role="tabpanel" aria-labelledby="ka-tab">
					<div class="mb-1">
						<x-form.input name="name_ka" label="სათაური" value="{{ old('title_ka', $serviceCategory->name->ka) }}"
							placeholder="შეიყვანეთ სათაური ქართულად" />
					</div>
				</div>

				<!-- English content -->
				<div class="tab-pane fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab">
					<div class="mb-1">
						<x-form.input name="name_en" label="Title" value="{{ old('title_en', $serviceCategory->name->en) }}"
							placeholder="შეიყვანეთ სათაური ინგლისურად" />
					</div>

				</div>
			</x-ui.tabs>
		</div>

		<!-- Visibility -->
		<div class="col-md-6 ">
			<x-form.select name="visibility" label="ხილვადობა" :options="['1' => 'ხილული', '0' => 'დამალული']"
				selected="{{ old('visibility', $serviceCategory->visibility) }}" />
		</div>
	</x-admin.crud.form-container>
@endsection