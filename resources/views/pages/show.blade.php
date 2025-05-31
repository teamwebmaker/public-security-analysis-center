@extends('layouts.master')
@section('title', $item->title->$language)
@section('styles')
    <style>
        body {
            font-family: inherit;
        }

        .partners {
            padding-block: 15px;
        }

        .swiper-pagination {
            --swiper-pagination-bottom: -5px;
        }
    </style>
@endsection
@section('main')
    <div class="container-fluid my-4">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <x-single-item-component :item="$item" :language="$language" :category="$category" />
                </div>
            </div>
        </div>
    </div>
    @include('partials.partners')
@endsection
@section('scripts')
    <script>
        const swiper = new Swiper(".partners", partnersSliderParams);
    </script>
@endsection