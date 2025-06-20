@extends('layouts.dashboard')
@section('title', 'პროგრამების სია')

@section('main')
	<!-- success display -->
	@session('success')
		<div class="alert alert-success alert-dismissible fade show" role="alert" x-data="{ show: true }" x-show="show"
			x-init="setTimeout(() => show = false, 3000)">
			{{ $value }}
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	@endsession

	<div class="row g-4">
		@foreach($programs as $program)
			<x-admin.programs.program-card :program="$program" />
		@endforeach
	</div>

	<div class="row mt-4">
		<div class="col-md-12">
			<nav aria-label="Page navigation">
				{!! $programs->withQueryString()->links('pagination::bootstrap-5') !!}
			</nav>
		</div>
	</div>
@endsection

@section('scripts')
	{!! load_script('scripts/program/programIndex.js') !!}
@endsection