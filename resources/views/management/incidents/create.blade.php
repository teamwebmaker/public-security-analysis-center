@extends('management.master')

@section('title', 'ინციდენტის შექმნა')

@section('main')
    @include('incidents.partials.form', [
        'formTitle' => 'ინციდენტის შექმნა',
        'formAction' => route('management.incidents.store'),
        'formMethod' => 'POST',
        'backRoute' => route('management.incidents.index'),
        'incident' => null,
    ])
@endsection
