@extends('layouts.master')
@section('title', __('static.pages.home.title'))
@section('head-custom')
	{!! load_style('styles/bpg-paata.css') !!}
@endsection

@section('main')
	<main>
		<div class="container-fluid bg-light" style="padding-block: 100px;">
			<div class="text-center position-relative">

				<!-- Logo as background layer -->
				<div class="position-absolute top-50 start-50 translate-middle w-100 z-10">
					<img src="{{ asset('images/themes/logo.png') }}" alt="psac-icon" class="img-fluid mx-auto d-block"
						style="opacity: 0.4; max-width: 200px;">
				</div>

				<!-- Text above -->
				<h1 class="fw-bold fs-4 fs-md-3 font-paata position-relative z-20">
					{{ __('static.pages.home.heading') }}
				</h1>

			</div>
		</div>



		<div class="container my-5">
			<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between py-4">
				<h2 class="mb-3 fs-3 mb-md-0">{{ __('static.pages.home.headline') }}</h2>

				<!-- Filter -->
				@if ($articles->isNotEmpty())
					<x-sort-data name="sort" :selected="request()->query('sort', 'newest')" />
				@endif
			</div>

			<!-- Empty State -->
			@if ($articles->isEmpty())
				<x-ui.empty-state-message minHeight="60dvh" />
			@endif

			<!-- Responsive Grid -->
			<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
				@foreach($articles as $article)
					<div class="col">
						<x-card-component :title="json_decode($article->title)->$language"
							:description="json_decode($article->description)->$language" :image="implode('/', ['images', $article->collection, $article->image])" :link="route(implode('.', [$article->collection, 'show']), ['id' => $article->id])" />
					</div>
				@endforeach
			</div>

			<!-- Pagination -->
			@if($articles->hasPages())
				<div class="row mt-4">
					<div class="col-md-12 ">
						{{ $articles->withQueryString()->links('pagination::bootstrap-5') }}
					</div>
				</div>
			@endif
		</div>
	</main>
@endsection