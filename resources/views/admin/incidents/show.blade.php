@extends('layouts.admin.admin-dashboard')

@section('title', $incident->title)

@section('main')
    @include('incidents.partials.show-content')
@endsection
