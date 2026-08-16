@extends('layouts.admin.admin-dashboard')

@section('title', 'მატერიალურ-ტექნიკური აღჭ-ის რედაქტირება')

@section('main')
    @include('material-equipments.partials.form', [
        'formTitle' => 'მატერიალურ-ტექნიკური აღჭ-ის რედაქტირება',
        'formAction' => route('material-equipments.update', $materialEquipment),
        'formMethod' => 'PUT',
        'backRoute' => route('material-equipments.show', $materialEquipment),
    ])
@endsection
