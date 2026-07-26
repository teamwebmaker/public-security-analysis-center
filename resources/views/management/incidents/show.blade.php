@extends('management.master')

@section('title', $incident->title)

@section('main')
    @include('incidents.partials.show-content')
@endsection
