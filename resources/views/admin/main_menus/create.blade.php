@extends('layouts.dashboard')
@section('title', 'მენიუს შექმნა')
@section('main')

	<x-admin.crud.form-container title="მენიუს შექმნა" action="{{ route($resourceName . '.store') }}" method="POST"
		:hasFileUpload="true" :backRoute="$resourceName . '.index'">

		<!-- Multilingual Tabs -->
		<div class="mb-4 shadow-sm">
			<x-ui.tabs :tabs="[
			['id' => 'ka', 'label' => 'KA'],
			['id' => 'en', 'label' => 'EN'],
		]">
				<!-- Georgian content -->
				<div class="tab-pane fade show active " id="ka-tab-content" role="tabpanel" aria-labelledby="ka-tab">
					<div class="mb-1">
						<x-form.input name="title_ka" label="სათაური" value="{{ old('title_ka') }}"
							placeholder="შეიყვანეთ სათაური ქართულად" />
					</div>
				</div>

				<!-- English content -->
				<div class="tab-pane fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab">
					<div class="mb-1">
						<x-form.input name="title_en" label="Title" value="{{ old('title_en') }}"
							placeholder="შეიყვანეთ სათაური ინგლისურად" />
					</div>
				</div>
			</x-ui.tabs>
		</div>

		<div class="row">
			<!-- sorted -->
			<div class="col-md-6">
				<x-form.input type="number" id="sorted" name="sorted" label="რიგითობა" value="{{ old('sorted') }}"
					placeholder="რიგის ნომერი" min="1" />
			</div>

			<!-- Visibility -->
			<div class="col-md-6">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']" selected="1"
					label="ხილვადობა" />
			</div>
		</div>
	</x-admin.crud.form-container>

@endsection