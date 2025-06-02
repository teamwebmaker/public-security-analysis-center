@extends('layouts.master')
@section('title', 'Publications Page')


@section('main')
	<main>
		<div class="container-fluid pt-5">
			<div class="container-xxl">
				<div class="row mb-5">
					@foreach($publications as $publication)
						<div class="col-lg-4 col-md-6 mb-4">
							<x-card-component :title="$publication->title->$language"
								:description="$publication->description->$language" :image="'images/publications/' . $publication->image" :date="$publication->created" :link="route('publications.show', ['id' => $publication->id])" />

						</div>
					@endforeach
				</div>
				<div class="row">
					<div class="col-md-12">
						{!! $publications->withQueryString()->links('pagination::bootstrap-5') !!}
					</div>
				</div>
			</div>
		</div>
	</main>
@endsection