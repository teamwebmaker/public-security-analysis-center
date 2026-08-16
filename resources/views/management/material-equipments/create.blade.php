@extends('management.master')

@section('title', 'მატერიალურ-ტექნიკური აღჭ-ის შექმნა')

@section('main')
    @include('material-equipments.partials.form', [
        'formTitle' => 'მატერიალურ-ტექნიკური აღჭ-ის შექმნა',
        'formAction' => route('management.material-equipments.store'),
        'formMethod' => 'POST',
        'backRoute' => route('management.material-equipments.index'),
        'materialEquipment' => null,
    ])
@endsection
