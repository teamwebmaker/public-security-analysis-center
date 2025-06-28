@extends('layouts.dashboard')
@section('title', 'რედაქტირება: ' . $syllabus->title->ka)

@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT" action="{{ route('syllabuses.update', $syllabus) }}"
		title="სილაბუსის რედაქტირება" :hasFileUpload="true" backRoute="syllabuses.index">

		<x-slot name="imageHeader">
			@if($syllabus->pdf)
				<iframe src="{{ asset('documents/syllabuses/' . $syllabus->pdf) }}" style="height: 350px"></iframe>
			@endif
		</x-slot>

		<div class="mb-4">
			<x-tabs :tabs="[
			['id' => 'ka', 'label' => 'KA'],
			['id' => 'en', 'label' => 'EN'],
		]">
				<!-- Georgian content -->
				<div class="tab-pane fade show active " id="ka-tab-content" role="tabpanel" aria-labelledby="ka-tab">
					<div class="mb-3">
						<x-form.input name="title_ka" label="სათაური" placeholder="შეიყვანეთ სათაური ქართულად"
							value="{{ old('title_ka', $syllabus->title->ka) }}" />
					</div>

				</div>

				<!-- English content -->
				<div class="tab-pane fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab">
					<div class="mb-3">
						<x-form.input name="title_en" label="Title" placeholder="შეიყვანეთ სათაური ინგლისურად"
							value="{{ old('title_en', $syllabus->title->en) }}" />
					</div>
				</div>

			</x-tabs>
		</div>

		<!--  PDF -->
		<div class="row mb-4">
			<div class="col-md-6">
				<x-form.input type="file" name="pdf" label="pdf დოკუმენტი" :isImage="false" accept="application/pdf"
					:required="false" />
			</div>
			<!-- program_id -->
			<div class="col-md-6">
				<x-form.select name="program_id" id="program_id" :options="$programs"
					selected="{{ old('program_id', $syllabus->program_id) }}" label="პროგრამა" />
			</div>

		</div>
		<!-- Visibility -->
		<div class="row mb-2">
			<div class="col-md-4">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']"
					selected="{{ old('visibility', $syllabus->visibility) }}" label="ხილვადობა" />
			</div>
		</div>
	</x-admin.crud.form-container>
@endsection