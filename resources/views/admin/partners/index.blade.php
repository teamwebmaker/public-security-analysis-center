@extends('layouts.dashboard')

@section('title', 'პარტნიორების სია')

<x-admin.index-view :items="$partners">
    @foreach($partners as $partner)
        <x-admin-card :document="$partner" :image="$partner->image" :resourceName='$resourceName' />
    @endforeach
</x-admin.index-view>

@section('scripts')
    <script></script>
@endsection