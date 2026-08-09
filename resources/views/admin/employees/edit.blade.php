@extends('layouts.admin.admin-dashboard')

@section('title', 'თანამშრომლის რედაქტირება')

@section('main')
    @include('employees.partials.form', [
        'formTitle' => 'თანამშრომლის რედაქტირება',
        'formAction' => route('employees.update', $employee),
        'formMethod' => 'PUT',
        'backRoute' => route('employees.show', $employee),
    ])
@endsection
