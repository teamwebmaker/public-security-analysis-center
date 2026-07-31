@extends('layouts.admin.admin-dashboard')

@section('title', $order->title)

@section('main')
    @include('orders.partials.show-content')
@endsection
