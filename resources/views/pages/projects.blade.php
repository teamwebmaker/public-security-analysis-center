@extends('layouts.master')
@section('title',  'Projects Page')

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
        <div class="container-fluid pt-5">
            <div class="container-xxl">
                <div class="row mb-5">
                    @foreach($projects as $project)
                        <div class="col-md-4  mb-4">
                            <x-project-component :project="$project" language="ka"/>
                        </div>
                    @endforeach
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {!! $projects->withQueryString()->links('pagination::bootstrap-5') !!}
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
