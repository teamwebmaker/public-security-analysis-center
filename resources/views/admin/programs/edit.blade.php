@extends('layouts.dashboard')
@section('title', 'პროგრამის რედაქტირება')

@section('main')
	<div class="row justify-content-center">
		<div class="col p-0 p-sm-3 col-lg-10">
			<!-- Global Success Message -->
			@session('success')
				<div class="alert alert-success alert-dismissible fade show mb-4" role="alert" x-data="{ show: true }"
					x-show="show" x-init="setTimeout(() => show = false, 5000)">
					<i class="bi bi-check-circle-fill me-2"></i> {{ $value }}
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			@endsession
			<!-- Global Error Display -->
			@if ($errors->any())
				<div class="alert alert-danger mb-4">
					<ul class="mb-0">
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif

			<div class="card shadow-sm border-0">
				<!-- Existing Images Display -->
				<div class="row g-0">
					@if($program->image)
						<div class="col-md-6">
							<div class="card-header bg-transparent border-0 text-center position-relative p-0"
								style="min-height: 200px;">
								<a href="{{ asset('images/programs/' . $program->image) }}" data-fancybox
									data-caption="{{ $program->title->ka }}" class="d-block w-100 h-100">
									<img src="{{ asset('images/programs/' . $program->image) }}"
										class="img-fluid rounded-top w-100 h-100 object-fit-cover"
										style="max-height: 200px; object-position: center;">
								</a>
							</div>
						</div>
					@endif
					@if($program->certificate_image)
						<div class="col-md-6">
							<div class="card-header bg-transparent border-0 text-center position-relative p-0"
								style="min-height: 200px; ">
								<a href="{{ asset('images/certificates/programs/' . $program->certificate_image) }}" data-fancybox
									data-caption="სერთიფიკატი" class="d-block w-100 h-100">
									<img src="{{ asset('images/certificates/programs/' . $program->certificate_image) }}"
										class="img-fluid rounded-top w-100 h-100 object-fit-cover"
										style="max-height: 200px; object-position: center;">
								</a>
							</div>
						</div>
					@endif
				</div>

				<div class="card-body">
					<form method="POST" action="{{ route('programs.update', $program) }}" enctype="multipart/form-data"
						id="program-form" class="needs-validation" novalidate>
						@csrf
						@method('PUT')

						<div class="mb-4">
							<h1 class="h4">პროგრამის რედაქტირება</h1>
						</div>
						<!-- Title and Description (Multilingual) -->
						<div class="mb-4">
							<ul class="nav nav-tabs" id="lang-tabs" role="tablist">
								<li class="nav-item" role="presentation">
									<button class="nav-link active" id="ka-tab" data-bs-toggle="tab" data-bs-target="#ka"
										type="button" role="tab">
										KA
									</button>
								</li>
								<li class="nav-item" role="presentation">
									<button class="nav-link" id="en-tab" data-bs-toggle="tab" data-bs-target="#en" type="button"
										role="tab">
										EN
									</button>
								</li>
							</ul>
							<div class="tab-content p-3 border border-top-0 rounded-bottom">
								<!-- KA Content -->
								<div class="tab-pane  fade show active" id="ka" role="tabpanel">
									<div class="mb-3">
										<label for="title-ka" class="form-label">სათაური <span class="text-danger">*</span></label>
										<input type="text" id="title-ka" name="title[ka]"
											class="form-control @error('title.ka') is-invalid @enderror"
											placeholder="შეიყვანეთ სათაური ქართულად"
											value="{{ old('title.ka', $program->title->ka) }}" required>
										@error('title.ka')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
									<div class="mb-3">
										<label for="description-ka" class="form-label">აღწერა <span
												class="text-danger">*</span></label>
										<textarea id="description-ka" name="description[ka]"
											class="form-control @error('description.ka') is-invalid @enderror" rows="5"
											placeholder="შეიყვანეთ აღწერა ქართულად" minlength="10"
											required>{{ old('description.ka', $program->description->ka) }}</textarea>
										@error('description.ka')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>

								<!-- EN Content -->
								<div class="tab-pane fade" id="en" role="tabpanel">
									<div class="mb-3">
										<label for="title-en" class="form-label">Title <span class="text-danger">*</span></label>
										<input type="text" id="title-en" name="title[en]"
											class="form-control @error('title.en') is-invalid @enderror"
											placeholder="Enter title in English" value="{{ old('title.en', $program->title->en) }}"
											required>
										@error('title.en')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
									<div class="mb-3">
										<label for="description-en" class="form-label">Description <span
												class="text-danger">*</span></label>
										<textarea id="description-en" name="description[en]"
											class="form-control @error('description.en') is-invalid @enderror" rows="5"
											placeholder="Enter description in English" minlength="10"
											required>{{ old('description.en', $program->description->en) }}</textarea>
										@error('description.en')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
							</div>
						</div>

						<!-- Image Uploads -->
						<div class="row mb-4 align-items-start">
							<div class="col-md-6 mb-3 mb-md-0">
								<div class="card h-100 border">
									<div class="card-body">
										<label for="program-image" class="form-label">პროგრამის ახალი სურათი</label>
										<input type="file" id="program-image" name="image"
											class="form-control @error('image') is-invalid @enderror"
											accept="image/jpeg,image/png,image/webp">
										@error('image')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
										<div class="form-text">მხარდაჭერილი ფორმატები: JPG, PNG, WEBP. მაქსიმალური ზომა: 5MB
										</div>
										<div class="mt-2 text-center">
											<img id="program-image-preview" src="#" alt="Image preview" class="img-thumbnail d-none"
												style="max-height: 150px;" data-fancybox>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="card h-100 border align-items-start">
									<div class="card-body">
										<label for="certificate-image" class="form-label">სერთიფიკატის ახალი სურათი</label>
										<input type="file" id="certificate-image" name="certificate_image"
											class="form-control @error('certificate_image') is-invalid @enderror"
											accept="image/jpeg,image/png,image/webp">
										@error('certificate_image')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
										<div class="form-text">მხარდაჭერილი ფორმატები: JPG, PNG, WEBP. მაქსიმალური ზომა: 5MB
										</div>
										<div class="mt-2 text-center">
											<img id="certificate-image-preview" src="#" alt="Certificate preview"
												class="img-thumbnail d-none" style="max-height: 150px;" data-fancybox>
										</div>
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
										<label for="video" class="form-label">ვიდეო ბმული <span class="text-danger">*</span></label>
										<div class="input-group">
											<span class="input-group-text"><i class="bi bi-youtube"></i></span>
											<input type="url" id="video" name="video"
												class="form-control @error('video') is-invalid @enderror"
												placeholder="https://youtube.com/..." value="{{ old('video', $program->video) }}"
												required>
										</div>
										@error('video')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-md-4 mb-3">
										<label for="price" class="form-label">ფასი (₾) <span class="text-danger">*</span></label>
										<div class="input-group">
											<input type="number" id="price" name="price"
												class="form-control @error('price') is-invalid @enderror" placeholder="0.00"
												step="0.01" min="0" value="{{ old('price', $program->price) }}" required>
											<span class="input-group-text">₾</span>
										</div>
										@error('price')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-md-4 mb-3">
										<label for="duration" class="form-label">ხანგრძლივობა <span
												class="text-danger">*</span></label>
										<input type="text" id="duration" name="duration"
											class="form-control @error('duration') is-invalid @enderror" placeholder="მაგ: 2 კვირა"
											value="{{ old('duration', $program->duration) }}" required>
										@error('duration')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
								<!-- Location Field -->
								<div class="row">
									<div class="col-md-12 mb-3">
										<label for="address" class="form-label">მდებარეობა <span class="text-danger">*</span></label>
										<input type="text" id="address" name="address"
											class="form-control @error('address') is-invalid @enderror"
											placeholder="შეიყვანეთ პროგრამის მდებარეობა"
											value="{{ old('address', $program->address) }}" required>
										@error('address')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
										<div class="form-text ps-1">მაგ: ქუჩის სახელი, შენობის ნომერი, ქალაქი</div>
									</div>
								</div>
							</div>
						</div>

						<!-- Schedule Section -->
						<div class="card mb-4 border  overflow-hidden">
							<div class="card-header bg-light">
								<h6 class="mb-0">განრიგი</h6>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-md-6 mb-3">
										<label for="start-date" class="form-label">საწყისი თარიღი <span
												class="text-danger">*</span></label>
										<input type="date" id="start-date" name="start_date"
											class="form-control @error('start_date') is-invalid @enderror"
											value="{{ old('start_date', $program->start_date) }}" min="{{ date('Y-m-d') }}" required>
										@error('start_date')
											<div class=" invalid-feedback">{{ $message }}
											</div>
										@enderror
									</div>
									<div class="col-md-6 mb-3">
										<label for="end-date" class="form-label">დასასრული თარიღი <span
												class="text-danger">*</span></label>
										<input type="date" id="end-date" name="end_date"
											class="form-control @error('end_date') is-invalid @enderror"
											value="{{ old('end_date', $program->end_date) }}" min="{{ date('Y-m-d') }}" required>
										@error('end_date')
											<div class=" invalid-feedback">{{ $message }}
											</div>
										@enderror
									</div>
								</div>
								<!-- Time Section -->
								<div class="row" x-data="{
															   startTime: '{{ old('hour.start', $program->hour ? $program->hour->start : '') }}',
															   endTime: '{{ old('hour.end', $program->hour ? $program->hour->end : '') }}',
															   get timeError() {
															   return this.startTime && this.endTime && this.endTime <= this.startTime 
															   ? 'დასრულების დრო უნდა აღემატებოდეს დაწყების დროს' 
															   : null;
															   }
															   }">
									<div class="col-md-6 mb-3">
										<label for="start-time" class="form-label">დაწყების დრო <span
												class="text-danger">*</span></label>
										<input type="time" id="start-time" name="hour[start]"
											class="form-control @error('hour.start') is-invalid @enderror" x-model="startTime"
											value="{{ old('hour.start', $program->hour ? $program->hour->start : '') }}" required>
										@error('hour.start')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-md-6 mb-3">
										<label for="end-time" class="form-label">დასრულების დრო <span
												class="text-danger">*</span></label>
										<input type="time" id="end-time" name="hour[end]"
											class="form-control @error('hour.end') is-invalid @enderror" x-model="endTime"
											x-bind:min="startTime"
											value="{{ old('hour.end', $program->hour ? $program->hour->end : '') }}" required>
										@error('hour.end')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
										<template x-if="timeError">
											<div class="text-danger small mt-1" x-text="timeError"></div>
										</template>
									</div>
								</div>
								<!-- Days Selection -->
								<div class="mb-3">
									<label class="form-label">კვირის დღეები <span class="text-danger">*</span></label>
									<div class="d-flex flex-wrap gap-2">
										@php
											$oldDays = old('days', $program->days->ka ?? []);
											$days = ['ორშაბათი', 'სამშაბათი', 'ოთხშაბათი', 'ხუთშაბათი', 'პარასკევი', 'შაბათი', 'კვირა'];
										@endphp
										@foreach($days as $day)
											<div class="form-check form-check-inline">
												<input class="form-check-input" type="checkbox" name="days[]" value="{{ $day }}"
													id="day-{{ $loop->index }}" {{ in_array($day, $oldDays) ? 'checked' : '' }}>
												<label class="form-check-label" for="day-{{ $loop->index }}">{{ $day }}</label>
											</div>
										@endforeach
									</div>
									@error('days')
										<div class="text-danger small">{{ $message }}</div>
									@enderror
									<div class="form-text ps-1">გთხოვთ შეარჩიოთ მინიმუმ ერთი დღე</div>
								</div>
							</div>
						</div>

						<!-- Visibility & Submit -->
						<div class="row">
							<div class="col-md-3 mb-3">
								<label for="visibility" class="form-label">ხილვადობა</label>
								<select id="visibility" name="visibility"
									class="form-select @error('visibility') is-invalid @enderror">
									<option value="1" {{ old('visibility', $program->visibility ?? '1') == '1' ? 'selected' : ''
																   }}>
										ხილული
									</option>
									<option value="0" {{ old('visibility', $program->visibility ?? '1') == '0' ? 'selected' : ''
																   }}>
										დამალული
									</option>
								</select>
								@error('visibility')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
						</div>

						<!-- Action buttons -->
						<div class="d-flex justify-content-end align-items-center flex-column flex-sm-row gap-2 mt-4">
							<x-go-back-button fallback="programs.index" />
							<button type="submit" class="btn btn-primary px-4">
								<i class="bi bi-check-lg me-2"></i> განახლება
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('scripts')
	{!! load_script('scripts/program/programEdit.js') !!}
@endsection