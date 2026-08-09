@extends('layouts.admin.admin-dashboard')

@section('title', 'თანამშრომლის შექმნა')

@section('main')
    @include('employees.partials.form', [
        'formTitle' => 'თანამშრომლის შექმნა',
        'formAction' => route('employees.store'),
        'formMethod' => 'POST',
        'backRoute' => route('employees.index'),
        'employee' => null,
        'canChangePersonalDetailsVisibility' => true,
    ])
@endsection
