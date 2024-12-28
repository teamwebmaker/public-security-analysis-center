@extends('layouts.master')
@section('title',  'Home Page')

@section('styles')
    <style>
        .partners {
            padding-block: 15px;
        }
        .swiper-pagination {
            --swiper-pagination-bottom: -5px;
        }
    </style>
@endsection

@section('main')
<main>
    @include('partials.articles')
    @include('partials.partners')
</main>
@endsection

@section('scripts')
    <script>
        const swiper = new Swiper(".partners", partnersSliderParams);
    </script>
@endsection
