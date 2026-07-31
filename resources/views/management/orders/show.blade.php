@extends('management.master')

@section('title', $order->title)

@section('main')
    @include('orders.partials.show-content')
@endsection
