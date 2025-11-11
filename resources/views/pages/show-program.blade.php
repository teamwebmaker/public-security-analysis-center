@extends('layouts.master')
@section('title', $item->title->$language)

@section('main')
	<!-- Global Success Display -->
	@if(session()->has('success'))
		<x-ui.toast :messages="[session('success')]" type="success" />
	@endif

	<!-- Global Error Display -->
	@if ($errors->any())
		<x-ui.toast :messages="$errors->all()" type="error" />
	@endif

	<!-- Course Header -->
	<section class="gold-bg  rounded-bottom-4">
		<div class="container py-5 px-3 px-sm-5">
			<div class="row g-3 g-sm-5 align-items-center">

				<!-- Media Column -->
				<div class="col-12 col-lg-6">
					<div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm">
						@if (!empty($item->video))
							@php
								preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([^&\s]+)/', $item->video, $matches);
								$youtubeId = $matches[1] ?? null;
							@endphp
							@if ($youtubeId)
								<iframe src="https://www.youtube.com/embed/{{ $youtubeId }}" title="Course video" frameborder="0"
									allowfullscreen class="w-100 h-100">
								</iframe>
							@endif
						@elseif (!empty($item->image))
							<img src="{{ asset(implode('/', ['images', $resourceName, $item->image])) }}"
								class="w-100 h-100 object-fit-cover" alt="{{ $item->title->$language }}">
						@endif
					</div>
				</div>
				<!-- Info Column -->
				<div class="col-12 col-lg-6">
					<h1 class="fw-bold mb-4 fs-3 gold-black">{{ $item->title->$language }}</h1>

					<ul class="list-unstyled fs-6 lh-lg">
						<li class="mb-2 me-2">
							<i class="bi bi-currency-exchange me-2"></i>
							<strong class="pe-1">{{__('static.pages.programs.details.price')}}:</strong> {{ $item->price }} ₾
						</li>
						<li class="mb-2 me-2">
							<i class="bi bi-clock-fill me-2"></i>
							<strong class="pe-1">{{__('static.pages.programs.details.duration')}}:</strong>
							{{ $item->duration }}
						</li>
						<li class="mb-2 me-2">
							<i class="bi bi-calendar-event-fill me-2"></i>
							<strong class="pe-1">{{__('static.pages.programs.details.starting_date')}}:</strong>
							{{ date('d.m.Y', strtotime($item->start_date)) }}
						</li>
						<li class="mb-2 me-2">
							<i class="bi bi-calendar-week-fill mb-2 me-2"></i>
							<strong class="pe-1">{{__('static.pages.programs.details.schedule')}}:</strong>
							{{ collect($item->days->$language)->map(fn($day) => mb_substr(trim($day), 0, 3))->implode(', ') }}
							|
							{{ $item->hour->start }} - {{ $item->hour->end }}
						</li>

						@if ($item->address)
							<li class="mb-2 me-2 d-flex align-items-center">
								<i class="bi bi-geo-alt-fill me-2"></i>
								<strong class="pe-1">{{__('static.pages.programs.details.location')}}:</strong>
								<span class="text-truncate d-inline-block" style="max-width: 150px;" data-bs-toggle="tooltip"
									data-bs-placement="top" title="{{ $item->address }}">
									<a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($item->address) }} "
										target="_blank">
										{{ $item->address }}
									</a>
								</span>
							</li>
						@endif

					</ul>

					<div class="mt-4 d-flex flex-wrap gap-3">
						<a href="#" data-bs-toggle="modal" data-bs-target="#register_to_program_modal"
							class="btn view-more--secondary px-4">{{__('static.register.title')}}</a>
						<x-modal id="register_to_program_modal" :title="__('static.pages.programs.register_to_program')"
							size="md" height="min-content">
							<x-register-to-program-form />
						</x-modal>
					</div>
				</div>

			</div>
		</div>
	</section>

	<!-- About Section -->
	<section class="container py-5 px-3 px-sm-5">
		<h2 class="gold-text fw-bold mb-4">{{__('static.pages.programs.details.about')}}</h2>
		<div class="row g-4 align-items-start">
			<div class="col-md-9">
				<p class="fs-5 fw-light lh-md justified-text">
					{{ $item->description->$language }}
				</p>
			</div>
			@if ($item->certificate_image)
				<div class="col-7 col-sm-5 col-md-3 ">
					<img src="{{ asset(implode('/', ['images/certificates', $resourceName, $item->certificate_image])) }}"
						class="img-fluid rounded shadow" alt="certificate-{{ $item->title->$language }} ">
				</div>
			@endif

		</div>
	</section>

	<!-- Syllabus Section -->
	@if(isset($item->syllabuses) && count($item->syllabuses) > 0)
		<section class="border-top border border-gold-bg--light">
			<div class="container py-5 px-3 px-sm-5">
				<h2 class="gold-text fw-bold mb-4">{{__('static.pages.programs.syllabus')}}</h2>
				<ul class="list-group">
					@foreach ($item->syllabuses as $index => $syllabus)
						<li
							class="col-12 list-group-item col-sm-8 col-lg-5 mb-3 border rounded   p-3 d-flex justify-content-between align-items-center bg-white ">
							<a href="#" data-bs-toggle="modal" data-bs-target="#pdfModal{{ $index }}" class="text-decoration-none">
								{{ $syllabus->title->$language }}

							</a>
						</li>

						<!-- Modal for PDF Viewer -->
						<x-modal :id="'pdfModal' . $index" :title="$syllabus->title->$language" size="xl">
							<iframe src="{{ asset('documents/' . $program_syllabuses . '/' . $syllabus->pdf) }}"
								class="w-100 h-100 border-0" allowfullscreen></iframe>
						</x-modal>

					@endforeach
				</ul>
			</div>
		</section>
	@endif

	<!-- Instructors Section -->
	@if (isset($item->mentors) && count($item->mentors) > 0)
		<section class="bg-light py-5 rounded-top-5">
			<div class="container px-3 px-sm-5">
				<h2 class="fw-bold mb-5 gold-text text-center ">{{__('static.pages.programs.mentors')}}</h2>
				<div class="row justify-content-center g-4">
					@foreach($item->mentors as $mentor)
						<div class="col-12 col-md-6 col-lg-4 d-flex">
							<div class="card w-100 h-100 border-0 shadow-sm text-center p-4 hover-shadow transition">
								<div class="mx-auto mb-3 position-relative" style="width: 120px; height: 120px;">
									<img src="{{ asset('images/' . $resourceName_mentors . '/' . $mentor->image) }}"
										alt="{{ $mentor->full_name }}" class="rounded-circle  img-fluid"
										style="object-fit: cover; width: 100%; height: 100%;">
								</div>
								<div class="card-body px-0">
									<h5 class="card-title fw-semibold mb-1">{{ $mentor->full_name }}</h5>
									@if (is_object($mentor->description) && !empty($mentor->description->$language))
										<p class="card-text text-muted small mb-0">{{ $mentor->description->$language }}</p>
									@endif
								</div>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</section>
	@endif
@endsection

@section('scripts')
	{!! load_script('scripts/bootstrap/bootstrapValidation.js') !!}
@endsection