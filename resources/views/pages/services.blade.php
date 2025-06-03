@extends('layouts.master')
@section('title', 'Services Page')

@section('main')
	<main>
		{{-- Hero Section --}}
		<div class="container-fluid pt-5 pb-5 gold-bg border-bottom border-1 border-gold-bg--light rounded-bottom-4">
			<div class="container-xxl px-3 px-md-5">
				<div class="text-center mb-4">
					<h1 class="fw-bold display-5 text-white animate__animated animate__fadeInDown">Our Services</h1>
					{{-- Jump Links --}}
					<div class="d-flex justify-content-center flex-wrap gap-2 mt-4 mb-3">
						@foreach($categories as $category)
							<a href="#category-{{ Str::slug($category->name) }}"
								class="btn btn-outline-light rounded-pill px-4 py-2 d-flex align-items-center gap-2 shadow-sm hover-shadow">
								<span>{{ $category->name }}</span>
								<i class="bi bi-box-arrow-up-right"></i>
							</a>
						@endforeach
					</div>

					{{-- CTA Button / modal --}}
					<div class="mt-3">
						<a href="#" data-bs-toggle="modal" data-bs-target="#servicesModal"
							class="btn btn-light btn-lg px-4 rounded-pill d-inline-flex align-items-center gap-2 shadow-sm">
							მოითხოვე სერვისები <i class="bi bi-envelope-open"></i>
						</a>
						<x-modal id="servicesModal" :title="'Request a Service'" size="md">
							<x-services-form />
						</x-modal>
					</div>
				</div>
			</div>
		</div>

		{{-- Services Section --}}
		<div class="container-fluid py-5 bg-light">
			<div class="container-xxl px-3 px-md-5">
				@foreach($categories as $category)
					@if (!$category->services->isEmpty())
						<div id="category-{{ Str::slug($category->name) }}" class="mb-5 pt-4">
							<h3 class="text-center text-uppercase fw-semibold gold-text mb-4 animate__animated animate__fadeInUp">
								{{ $category->name }}
							</h3>
							<div class="row g-4">
								@foreach($category->services as $service)
									<div class="col-lg-4 col-md-6">
										{{-- img_temp | Temporary --}}
										<x-card-component :title="$service->title->$language" :description="$service->description->$language"
											:image="'images/services/' . $service->image" :img_temp="$service->image" :link="route('services.show', ['id' => $service->id])" />
									</div>
								@endforeach
							</div>
						</div>
					@endif
				@endforeach
			</div>
		</div>
	</main>
@endsection