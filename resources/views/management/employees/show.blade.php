@extends('management.master')

@section('title', $employee->name . ' ' . $employee->surname)

@section('main')
    @include('employees.partials.show-content')
@endsection
