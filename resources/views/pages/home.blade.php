@extends('layouts.master')
@section('title', 'Home Page')

@section('main')

    <!-- Global Error Display TEMP -->
    @if ($errors->any())
        <x-ui.toast :messages="$errors->all()" type="error" />
    @endif

    <main>
        @include('partials.articles')
    </main>
@endsection