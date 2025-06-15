@extends('layouts.master')
@section('title', 'Programs Page')

@section('main')
	<main>
		<div class="container-fluid pt-2">
			<div class="container-xxl">
				<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between py-4 ">
					<h2 class="mb-3 mb-md-0">პროგრამები</h2>

					<!-- Fixed Filter Component -->
					<x-sort-data name="sort" :options="['newest' => 'Newest to Oldest', 'oldest' => 'Oldest to Newest']"
						:selected="request()->query('sort', 'newest')" class="" />
				</div>
				<div class="row mb-5">
					@foreach($programs as $program)
						@if ($program->visibility)
							<div class="col-lg-4 col-md-6  mb-4">
								<x-card-component :title="$program->title->$language" :image="'images/programs/' . $program->image"
									:link="route('programs.show', ['id' => $program->id])" />
							</div>
						@endif
					@endforeach
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