@extends('management.master')

@section('title', 'ინციდენტის რედაქტირება')

@section('main')
    @include('incidents.partials.form', [
        'formTitle' => 'ინციდენტის რედაქტირება',
        'formAction' => route('management.incidents.update', $incident),
        'formMethod' => 'PUT',
        'backRoute' => route('management.incidents.show', $incident),
    ])
@endsection
