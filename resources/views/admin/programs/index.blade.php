@extends('layouts.dashboard')

@section('title', 'პროგრამების სია')

<x-admin.index-view :items="$programs" containerClass="row mt-4">
	@foreach($programs as $program)
		<x-admin-card :document="$program" :image="$program->image" :resourceName='$resourceName'
			containerClass="col-12 col-sm-8 col-md-6 mx-auto">
			<x-slot name="cardDetails">
				<x-admin.programs.details-list :program="$program" />
			</x-slot>
		</x-admin-card>
	@endforeach
</x-admin.index-view>

@section('scripts')
	{!! load_script('scripts/program/programIndex.js') !!}
@endsection