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
        <div class="container-fluid">
            <div class="container-sm">
                <div class="row">

                </div>
            </div>
        </div>
        @include('partials.partners')
    </main>
@endsection

@section('scripts')
    <script>
        const swiper = new Swiper(".partners", partnersSliderParams);
    </script>
@endsection
