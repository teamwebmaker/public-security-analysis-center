@extends('management.master')

@section('title', 'თანამშრომლის რედაქტირება')

@section('main')
    @include('employees.partials.form', [
        'formTitle' => 'თანამშრომლის რედაქტირება',
        'formAction' => route('management.employees.update', $employee),
        'formMethod' => 'PUT',
        'backRoute' => route('management.employees.show', $employee),
    ])
@endsection
