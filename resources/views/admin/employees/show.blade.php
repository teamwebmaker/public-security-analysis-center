@extends('layouts.admin.admin-dashboard')

@section('title', $employee->name . ' ' . $employee->surname)

@section('main')
    @include('employees.partials.show-content')
@endsection
