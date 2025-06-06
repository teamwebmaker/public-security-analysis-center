@extends('layouts.master')
@section('title', 'About us Page')
@section('main')
	<main>
		<div class="container my-5 shadow rounded p-4 bg-white">
			<!-- Title -->
			<div class="mb-4">
				<h2 class="gold-text fw-bold" style="letter-spacing: 0.5px;">
					{{ $item->title->$language }}
				</h2>
			</div>

			<!-- Row -->
			<div class="row align-items-start gx-lg-4 gy-4">
				<!-- Left Column: Text -->
				<div class="col-12 col-lg-6">
					<p class="fs-5 fw-light mb-0 justified-text" style="line-height: 1.7; color: #333;">
						{{ $item->description->$language }}
					</p>
				</div>

				<!-- Right Column: Image & Stats -->
				<div class="col-12 col-lg-6">
					<div class="d-flex flex-column flex-lg-column-reverse gap-3">
						<!-- Stats (Top on mobile, Bottom on large) -->
						@php
							$hasExperience = isset($item->experience);
							$hasGraduates = isset($item->graduates);
						@endphp

						@if ($hasExperience || $hasGraduates)
							<div
								class="border rounded py-3 px-3 bg-white d-flex flex-column flex-sm-row justify-content-between text-center gap-3">
								@if ($hasExperience)
									<div class="flex-fill">
										<h4 class="fw-bold gold-text mb-1">{{ $item->experience }}&nbsp;+</h4>
										<p class="mb-0 text-muted">Years Experience</p>
									</div>
								@endif

								@if ($hasExperience && $hasGraduates)
									<!-- Divider: horizontal on mobile, vertical on desktop -->
									<div class="d-block d-sm-none" style="height:1px; background:#ddd;"></div>
									<div class="d-none d-sm-block vr"></div>
								@endif

								@if ($hasGraduates)
									<div class="flex-fill">
										<h4 class="fw-bold gold-text mb-1">{{ $item->graduates }}&nbsp;+</h4>
										<p class="mb-0 text-muted">Graduates</p>
									</div>
								@endif
							</div>
						@endif

						<!-- Image (Bottom on mobile, Top on large) -->
						<div class="mt-2 d-flex justify-content-center align-items-center rounded overflow-hidden"
							style="max-height: 450px; max-width: 100%;">
							<img src="{{ asset(implode('/', ['images', $category, $item->image])) }}"
								alt="{{ $item->title->$language }}" class="img-fluid rounded object-fit-contain w-100">
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
@endsection