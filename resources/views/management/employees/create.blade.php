@extends('management.master')

@section('title', 'თანამშრომლის შექმნა')

@section('main')
    @include('employees.partials.form', [
        'formTitle' => 'თანამშრომლის შექმნა',
        'formAction' => route('management.employees.store'),
        'formMethod' => 'POST',
        'backRoute' => route('management.employees.index'),
        'employee' => null,
        'canChangePersonalDetailsVisibility' => true,
    ])
@endsection
