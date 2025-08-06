@extends('layouts.admin.admin-dashboard')
@section('title', 'სილაბუსის შექმნა')
@section('main')
	<x-admin.crud.form-container method="POST" title="სილაბუსის შექმნა" action="{{ route($resourceName . '.store') }}"
		:hasFileUpload="true" :backRoute="$resourceName . '.index'">

		<!-- Title ka en -->
		<div class="mb-4">
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

		<!-- PDF -->
		<div class="row mb-4">
			<div class="col-md-6">
				<x-form.input type="file" name="pdf" label="pdf დოკუმენტი" :isImage="false" accept="application/pdf" />
			</div>
			<!-- program_id -->
			<div class="col-md-6">
				<x-form.select name="program_id" id="program_id" :options="$programs" label="პროგრამა"
					:selected="old('program_id')" />
			</div>
		</div>

		<!-- Visibility -->
		<div class="row">
			<div class="col-md-4">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']" selected="1"
					label="ხილვადობა" />
			</div>
		</div>
	</x-admin.crud.form-container>
@endsection