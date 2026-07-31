@extends('layouts.admin.admin-dashboard')

@section('title', 'ბრძანების შექმნა')

@section('main')
    @include('orders.partials.form', [
        'formTitle' => 'ბრძანების შექმნა',
        'formAction' => route('orders.store'),
        'formMethod' => 'POST',
        'backRoute' => route('orders.index'),
        'order' => null,
    ])
@endsection
