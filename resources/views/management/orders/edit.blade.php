@extends('management.master')

@section('title', 'ბრძანების რედაქტირება')

@section('main')
    @include('orders.partials.form', [
        'formTitle' => 'ბრძანების რედაქტირება',
        'formAction' => route('management.orders.update', $order),
        'formMethod' => 'PUT',
        'backRoute' => route('management.orders.show', $order),
    ])
@endsection
