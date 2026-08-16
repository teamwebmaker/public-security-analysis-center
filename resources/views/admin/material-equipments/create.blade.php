@extends('layouts.admin.admin-dashboard')

@section('title', 'მატერიალურ-ტექნიკური აღჭ-ის შექმნა')

@section('main')
    @include('material-equipments.partials.form', [
        'formTitle' => 'მატერიალურ-ტექნიკური აღჭ-ის შექმნა',
        'formAction' => route('material-equipments.store'),
        'formMethod' => 'POST',
        'backRoute' => route('material-equipments.index'),
        'materialEquipment' => null,
    ])
@endsection
