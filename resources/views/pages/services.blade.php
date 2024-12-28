@extends('layouts.master')
@section('title',  'Services Page')

@section('styles')
    <style>
        .service-desc {
            font-size: 14px !important;
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
    <main>
        <div class="container-fluid pt-5">
            <div class="container-xxl">
                <div class="row mb-5">
                    @foreach($services as $service)
                        <div class="col-md-6  mb-4">
                            <x-service-component :service="$service" language="ka"/>
                        </div>
                    @endforeach
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {!! $services->withQueryString()->links('pagination::bootstrap-5') !!}
                    </div>
                </div>
                <div class="row">
                    @include('partials.partners')
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        const swiper = new Swiper(".partners", partnersSliderParams);
    </script>
@endsection
