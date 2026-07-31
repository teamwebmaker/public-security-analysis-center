@extends('layouts.admin.admin-dashboard')

@section('title', 'ბრძანების რედაქტირება')

@section('main')
    @include('orders.partials.form', [
        'formTitle' => 'ბრძანების რედაქტირება',
        'formAction' => route('orders.update', $order),
        'formMethod' => 'PUT',
        'backRoute' => route('orders.show', $order),
    ])
@endsection
