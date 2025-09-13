@extends('layouts.master')
@section('title', __('static.pages.programs.title'))

@section('main')
	<main>
		<div class="container-fluid pt-2">
			<div class="container-xxl">
				<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between py-4 ">
					<h2 class="mb-3 mb-md-0">{{ __('static.pages.programs.heading') }}</h2>

					<!-- Fixed Filter Component -->
					@if ($programs->isNotEmpty())
						<x-sort-data name="sort" :selected="request()->query('sort', 'newest')" class="" />
					@endif
				</div>
				<div class="row mb-5">
					@forelse ($programs as $program)
						<div class="col-lg-4 col-md-6  mb-4">
							<x-card-component :title="$program->title->$language" :description="$program->description->$language"
								:image="'images/programs/' . $program->image" :link="route('programs.show', ['id' => $program->id])" />
						</div>
					@empty
						<x-ui.empty-state-message />
					@endforelse
				</div>
				<div class="row">
					<div class="col-md-12">
						{!! $programs->withQueryString()->links('pagination::bootstrap-5') !!}
					</div>
				</div>
			</div>
		</div>
	</main>
@endsection