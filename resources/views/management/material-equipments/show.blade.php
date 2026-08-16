@extends('management.master')

@section('title', $materialEquipment->title)

@section('main')
    @include('material-equipments.partials.show-content')
@endsection
