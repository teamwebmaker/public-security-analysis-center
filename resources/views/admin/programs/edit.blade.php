@extends('layouts.dashboard')
@section('title', 'რედაქტირება: ' . $program->title->ka)

@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT" title="პროგრამის რედაქტირება"
		action="{{ route('programs.update', $program) }}" :hasFileUpload="true" backRoute="programs.index"
		cardWrapperClass="col col-lg-12">

		<x-slot name="imageHeader">
			<div class="row g-0">
				@if($program->image)
					<div class="col-md-6">
						<x-admin.image-header :src="$program->image" folder="programs" caption="პროგრამის ძველი სურათი" />
					</div>
				@endif
				@if($program->certificate_image)
					<div class="col-md-6">
						<x-admin.image-header :src="$program->certificate_image" folder="certificates/programs"
							caption="სერთიფიკატის ძველი სურათი" />
					</div>
				@endif
			</div>
		</x-slot>

		<!-- Multilingual Tabs -->
		<div class="mb-4">
			<x-tabs :tabs="[
			['id' => 'ka', 'label' => 'KA'],
			['id' => 'en', 'label' => 'EN'],
		]">
				<!-- Georgian content -->
				<div class="tab-pane fade show active " id="ka-tab-content" role="tabpanel" aria-labelledby="ka-tab">
					<div class="mb-3">
						<x-form.input name="title_ka" label="სათაური" value="{{ old('title_ka', $program->title->ka) }}"
							placeholder="შეიყვანეთ სათაური ქართულად" />
					</div>
					<div class="mb-1">
						<x-form.textarea name="description_ka" label="აღწერა"
							value="{{ old('description_ka', $program->description->ka) }}"
							placeholder="შეიყვანეთ აღწერა ქართულად" />
					</div>
				</div>

				<!-- English content -->
				<div class="tab-pane fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab">
					<div class="mb-3">
						<x-form.input name="title_en" label="Title" value="{{ old('title_en', $program->title->en) }}"
							placeholder="შეიყვანეთ სათაური ინგლისურად" />
					</div>
					<div class="mb-1">
						<x-form.textarea name="description_en" label="Description"
							value="{{ old('description_en', $program->description->en) }}"
							placeholder="შეიყვანეთ აღწერა ინგლისურად" />
					</div>
				</div>
			</x-tabs>
		</div>

		<!-- Image Uploads -->
		<div class="row mb-4 align-items-start overflow-hidden">
			<div class="col-md-6 mb-3 mb-md-0">
				<div class="card h-100 border">
					<div class="card-body">
						<x-form.input type="file" id="program-image" name="image" label="პროგრამის სურათი" :required="false" />
						<x-form.image-upload-preview id="program" />
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card h-100 border align-items-start">
					<div class="card-body">
						<x-form.input type="file" id="certificate-image" name="certificate_image" label="სერთიფიკატის სურათი"
							:required="false" />
						<x-form.image-upload-preview id="certificate" />
					</div>
				</div>
			</div>
		</div>

		<!-- Basic Information -->
		<div class="card mb-4 border overflow-hidden">
			<div class="card-header bg-light">
				<h6 class="mb-0">ძირითადი ინფორმაცია</h6>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-4 mb-3">
						<x-form.input type="url" name="video" label="ვიდეო ბმული" icon="youtube"
							placeholder="https://youtube.com/..." value="{{ old('video', $program->video) }}" />
					</div>
					<div class="col-md-4 mb-3">
						<x-form.input type="number" name="price" label="ფასი (₾)" placeholder="0.00"
							value="{{ old('price', $program->price) }}" step="0.01" min="0" />
					</div>
					<div class="col-md-4 mb-3">

						<x-form.input name="duration" label="ხანგრძლივობა" placeholder="მაგ: 2 კვირა"
							value="{{ old('duration', $program->duration) }}" />
					</div>
				</div>
				<!-- Location Field -->
				<div class="row">
					<div class="col-md-12 mb-3">

						<x-form.input name="address" label="მდებარეობა" placeholder="შეიყვანეთ მდებარეობა"
							value="{{ old('address', $program->address) }}" />
						<div class="form-text ps-1">მაგ: ქუჩის სახელი, შენობის ნომერი, ქალაქი</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Schedule Section -->
		<div class="card mb-4 border overflow-hidden">
			<div class="card-header bg-light">
				<h6 class="mb-0">განრიგი</h6>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6 mb-3">
						<x-form.input type="date" name="start_date" label="საწყისი დრო"
							value="{{ old('start_time', $program->start_date) }}" min="{{ date('Y-m-d') }}" />
					</div>
					<div class="col-md-6 mb-3">
						<x-form.input type="date" name="end_date" label="დასასრული თარიღი"
							value="{{ old('end_date', $program->end_date) }}" min="{{ date('Y-m-d') }}" />
					</div>
				</div>
				<div class="row" @php
					$start = old('hour_start', $program->hour->start ?? '');
					$end = old('hour_end', $program->hour->end ?? '');
				@endphp
					x-data="{ startTime: '{{ $start }}', endTime: '{{ $end }}', get timeError() { return this.startTime && this.endTime && this.endTime <= this.startTime ? 'დასრულების დრო უნდა აღემატებოდეს დაწყების დროს': null; }}">
					<div class="col-md-6 mb-3">
						<x-form.input type="time" name="hour_start" label="დაწყების დრო" value="{{ old('hour_start', $start) }}"
							x-model="startTime" />
					</div>
					<div class="col-md-6 mb-3">

						<x-form.input type="time" name="hour_end" label="დასრულების დრო" value="{{ old('hour_end', $end) }}"
							x-model="endTime" x-bind:min="startTime" />

						<!-- Alpine.js validation message -->
						<template x-if="timeError">
							<div class="text-danger small mt-1" x-text="timeError"></div>
						</template>
					</div>
				</div>
				<!-- Days Selection -->
				<x-form.checkbox-group name="days" :options="['ორშაბათი', 'სამშაბათი', 'ოთხშაბათი', 'ხუთშაბათი', 'პარასკევი', 'შაბათი', 'კვირა']" :selected="old('days', $program->days->ka ?? [])" label="კვირის დღეები" />
			</div>
		</div>

		<!-- Visibility & Submit -->
		<div class="row">
			<div class="col-md-3 mb-3">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']"
					selected="{{ old('visibility', $program->visibility) }}" label="ხილვადობა" />
			</div>
		</div>


	</x-admin.crud.form-container>
@endsection

@section('scripts')
	{!! load_script('scripts/program/programEdit.js') !!}
@endsection