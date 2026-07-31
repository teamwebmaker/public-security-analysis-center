@extends('management.master')

@section('title', 'ბრძანების შექმნა')

@section('main')
    @include('orders.partials.form', [
        'formTitle' => 'ბრძანების შექმნა',
        'formAction' => route('management.orders.store'),
        'formMethod' => 'POST',
        'backRoute' => route('management.orders.index'),
        'order' => null,
    ])
@endsection
