@extends('layouts.admin.admin-dashboard')
@section('title', 'რედაქტირება: ' . $info->title->ka)

@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT" action="{{ route($resourceName . '.update', $info) }}"
		title="პროექტის რედაქტირება" :hasFileUpload="true" :backRoute="$resourceName . '.index'">

		<x-slot name="imageHeader">
			@if ($info->image)
				<x-admin.image-header :src="$info->image" :folder="$resourceName" caption="პროექტის ძველი სურათი" />
			@endif
		</x-slot>

		<!-- Multilingual Tabs -->
		<div class="mb-4">
			<x-ui.tabs :tabs="[
			['id' => 'ka', 'label' => 'KA'],
			['id' => 'en', 'label' => 'EN'],
		]">
				<!-- Georgian content -->
				<div class="tab-pane fade show active " id="ka-tab-content" role="tabpanel" aria-labelledby="ka-tab">
					<div class="mb-3">
						<x-form.input name="title_ka" label="სათაური" placeholder="შეიყვანეთ სათაური ქართულად"
							value="{{ old('title_ka', $info->title->ka) }}" />
					</div>
					<div class="mb-1">
						<x-form.textarea name="description_ka" label="აღწერა" rows="8"
							value="{{ old('description_ka', $info->description->ka) }}" placeholder="შეიყვანეთ აღწერა ქართულად" />
					</div>
				</div>

				<!-- English content -->
				<div class="tab-pane fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab">
					<div class="mb-3">
						<x-form.input name="title_en" label="Title" placeholder="შეიყვანეთ სათაური ინგლისურად"
							value="{{ old('title_en', $info->title->en) }}" />
					</div>
					<div class="mb-1">
						<x-form.textarea name="description_en" label="Description" rows="8"
							value="{{ old('description_en', $info->description->en) }}"
							placeholder="შეიყვანეთ აღწერა ინგლისურად" />
					</div>
				</div>

			</x-ui.tabs>
		</div>

		<div class="row ">
			<!-- Experience -->
			<div class="col-md-6 mb-3">
				<x-form.input type='number' name="experience" label="გამოცდილება (წელი)" placeholder="მაგ: 10" min="0"
					value="{{ old('experience', $info->experience) }}" :required="false" />
			</div>

			<!-- Graduates -->
			<div class="col-md-6 mb-3">
				<x-form.input type='number' name="graduates" label="კურსდამთავრებული" placeholder="მაგ: 100" min="0"
					value="{{ old('graduates', $info->graduates) }}" :required="false" />

			</div>
		</div>
		<div class="row">
			<!-- email -->
			<div class="col-md-6 mb-3">
				<x-form.input type='email' name="email" label="ელ.ფოსტა" icon="envelope-at-fill	"
					placeholder="მაგ: example@email.com" value="{{ old('email', $info->email) }}" autocomplete="email" />
			</div>

			<!-- phone -->
			<div class="col-md-6 mb-3">
				<x-form.input type='tel' name="phone" label="ტელეფონი" icon="telephone-fill"
					placeholder="მაგ: +995 XXX XXX XXX, ან: 032 XXX XX XX"
					pattern="^(\+\d{3} \d{3} \d{3} \d{3}|\+\d{3} \d{3} \d{2} \d{2} \d{2}|\d{3} \d{3} \d{2} \d{2})$"
					value="{{ old('phone', $info->phone) }}" autocomplete="email" />

			</div>
		</div>

		<!-- Image -->
		<div class="col-md-5">
			<x-form.input type="file" id="image" name="image" label="სურათი" :required="false" />
			<x-form.image-upload-preview id="image" />
		</div>


	</x-admin.crud.form-container>
@endsection

@section('scripts')
	{!! load_script('scripts/imgUploadInit.js') !!}
@endsection