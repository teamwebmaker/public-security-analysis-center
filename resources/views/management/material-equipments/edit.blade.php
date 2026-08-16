@extends('management.master')

@section('title', 'მატერიალურ-ტექნიკური აღჭ-ის რედაქტირება')

@section('main')
    @include('material-equipments.partials.form', [
        'formTitle' => 'მატერიალურ-ტექნიკური აღჭ-ის რედაქტირება',
        'formAction' => route('management.material-equipments.update', $materialEquipment),
        'formMethod' => 'PUT',
        'backRoute' => route('management.material-equipments.show', $materialEquipment),
    ])
@endsection
