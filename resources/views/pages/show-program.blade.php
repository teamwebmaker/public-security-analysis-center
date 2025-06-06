@extends('layouts.master')
@section('title', $item->title->$language)

@section('main')
	<!-- Course Header -->
	<section class="gold-bg border-bottom border-1 border-gold-bg--light rounded-bottom-4">
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
							<img src="{{ asset(implode('/', ['images', $category, $item->image])) }}"
								class="w-100 h-100 object-fit-cover" alt="{{ $item->title->$language }}">
						@endif
					</div>
				</div>
				<!-- Info Column -->
				<div class="col-12 col-lg-6">
					<h1 class="fw-bold mb-4 fs-3 gold-black">{{ $item->title->$language }}</h1>

					<ul class="list-unstyled fs-6 lh-lg">
						<li class="d-flex align-items-center mb-2">
							<i class="bi bi-currency-exchange me-2"></i>
							<strong class="pe-1">Price:</strong> {{ $item->price }} ₾
						</li>
						<li class="d-flex align-items-center mb-2">
							<i class="bi bi-clock-fill me-2"></i>
							<strong class="pe-1">Duration:</strong> {{ $item->duration }}
						</li>
						<li class="d-flex align-items-center mb-2">
							<i class="bi bi-calendar-event-fill me-2"></i>
							<strong class="pe-1">Course Starts:</strong> {{ date('d.m.Y', strtotime($item->start_date)) }}
						</li>
						<li class="d-flex align-items-center">
							<i class="bi bi-calendar-week-fill me-2"></i>
							<strong class="pe-1">Schedule:</strong>
							{{ collect($item->days)->map(fn($day) => mb_substr(trim($day), 0, 3))->implode(', ') }}
							|
							{{ $item->hour->start }} - {{ $item->hour->end }}
						</li>
					</ul>

					<div class="mt-4 d-flex flex-wrap gap-3">
						<a href="#" class="btn view-more--secondary px-4">Register</a>
						<a href="#" class="btn border-dark px-4">For Companies</a>
					</div>
				</div>

			</div>
		</div>
	</section>

	<!-- About Section -->
	<section class="container py-5 px-3 px-sm-5">
		<h2 class="gold-text fw-bold mb-4">About the Course</h2>
		<div class="row g-4 align-items-start">
			<div class="col-md-9">
				<p class="fs-5 fw-light lh-md justified-text">
					{{ $item->description->$language }}
				</p>
			</div>
			<div class="col-7 col-sm-5 col-md-3 ">
				<img src="{{ asset(implode('/', ['images', $category, $item->certificate_image])) }}"
					class="img-fluid rounded shadow" alt="certificate-{{ $item->title->$language }} ">
			</div>
		</div>
	</section>

	<!-- Syllabus Section -->
	<section class="border-top border-1 border-gold-bg--light">
		<div class="container py-5 px-3 px-sm-5">
			<h2 class="gold-text fw-bold mb-4">Syllabus</h2>
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
						<iframe src="{{ asset('documents/' . $category . '/' . $syllabus->pdf) }}" class="w-100 h-100 border-0"
							allowfullscreen></iframe>
					</x-modal>

				@endforeach
			</ul>
		</div>
	</section>

	<!-- Instructors Section -->
	<section class="gold-bg--light rounded-top-5">
		<div class="container-lg mt-5 px-3 px-sm-5">
			<h2 class="fw-bold mb-4 text-center gold-text">Instructors</h2>
			<div class="row justify-content-center g-4 mb-5">
				@foreach($item->mentors as $mentor)
					<div class="col-12 col-md-6 col-lg-4 d-flex">
						<div class="card w-100 h-100 border-0 shadow-sm text-center p-3">
							<img src="{{ $mentor->image }}" class="rounded mx-auto mb-3"
								style="width: 100px; height: 100px; object-fit: cover;" alt="{{ $mentor['full-name'] }}">
							<div class="card-body">
								<h5 class="card-title fw-bold mb-1">{{ $mentor['full-name'] }}</h5>
								<p class="text-muted small mb-1">{{ $mentor->position ?? '' }}</p>
								<p class="card-text fs-6">{{ $mentor->description }}</p>
							</div>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</section>

@endsection