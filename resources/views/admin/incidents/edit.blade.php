@extends('layouts.admin.admin-dashboard')

@section('title', 'ინციდენტის რედაქტირება')

@section('main')
    @include('incidents.partials.form', [
        'formTitle' => 'ინციდენტის რედაქტირება',
        'formAction' => route('incidents.update', $incident),
        'formMethod' => 'PUT',
        'backRoute' => route('incidents.show', $incident),
    ])
@endsection
