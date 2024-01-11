@extends('layouts.master')
@section('title',  'About us Page')

@section('styles')
    <style>
        .partners {
            padding-block: 10px;
        }
        .swiper-pagination {
            --swiper-pagination-bottom: -5px;
        }
    </style>
@endsection

@section('main')
    <main>
        <h1>{{ __('static.page.title') }}</h1>
        @include('partials.partners')
    </main>
@endsection

@section('scripts')
    <script>
        const swiper = new Swiper(".partners", {
            slidesPerView: 5,
            speed: 1000,
            autoplay: {
                delay: 3000,
            },
            spaceBetween: 30,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
        });
    </script>
@endsection
