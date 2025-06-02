@extends('layouts.master')
@section('title', 'Programs Page')

@section('main')
	<main>
		<div class="container-fluid pt-5">
			<div class="container-xxl">
				<div class="row mb-5">
					@foreach($programs as $program)
						<div class="col-lg-4 col-md-6  mb-4">
							<x-card-component :title="$program->title->$language" :image="'images/programs/' . $program->image"
								:link="route('programs.show', ['id' => $program->id])" />
						</div>
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