@extends('layouts.master')
@section('title', __('static.pages.home.title'))

@section('main')
    <main>
        @include('partials.articles')
    </main>
@endsection