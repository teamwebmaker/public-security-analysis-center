@extends('layouts.dashboard')
@section('title', 'სერვის კატეგორიის შექმნა')
@section('main')

	<x-admin.crud.form-container title="სერვის კატეგორიის შექმნა" action="{{ route('service-categories.store') }}"
		method="POST" :hasFileUpload="false" backRoute="service-categories.index" cardWrapperClass="col col-md-8">
		<!-- title ka en -->
		<div class="mb-3">
			<x-tabs :tabs="[
			['id' => 'ka', 'label' => 'KA'],
			['id' => 'en', 'label' => 'EN'],
		]">
				<!-- Georgian content -->
				<div class="tab-pane fade show active" id="ka-tab-content" role="tabpanel" aria-labelledby="ka-tab">
					<div class="mb-3">
						<x-form.input name="name_ka" label="სათაური" value="{{ old('title_ka') }}"
							placeholder="შეიყვანეთ სათაური ქართულად" />
					</div>
				</div>

				<!-- English content -->
				<div class="tab-pane fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab">
					<div class="mb-3">
						<x-form.input name="name_en" label="Title" value="{{ old('title_en') }}"
							placeholder="შეიყვანეთ სათაური ინგლისურად" />
					</div>

				</div>
			</x-tabs>
		</div>

		<!-- Visibility -->
		<div class="col-md-6">
			<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']" selected="1" label="ხილვადობა" />
		</div>
	</x-admin.crud.form-container>
@endsection