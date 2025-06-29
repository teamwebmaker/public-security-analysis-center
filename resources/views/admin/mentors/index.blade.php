@extends('layouts.dashboard')

@section('title', 'პარტნიორების სია')

<x-admin.index-view :items="$mentors" :resourceName="$resourceName">
    @foreach($mentors as $mentor)
        @php
            // dd($mentor->full_name);
        @endphp
        <x-admin-card :document="$mentor" :title="$mentor->full_name" :image="$mentor->image"
            :resourceName='$resourceName' />
    @endforeach
</x-admin.index-view>

@section('scripts')
    <script></script>
@endsection