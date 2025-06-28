@extends('layouts.dashboard')
@section('title', 'Admin Dashboard')
@section('main')
    <div class="row align-items-center g-4">
        <!-- Grouped Service + Category Card -->
        <div class="col-12">
            <div class="card border border-white shadow-sm bg-light rounded-4 h-100 p-3  ">

                <!-- Optional title -->
                <h6 class="text-muted mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-layers text-secondary"></i>
                    სერვისები და კატეგორიები
                </h6>

                <!-- Side-by-side equal-width cards -->
                <div class="d-flex flex-column flex-md-row gap-3">
                    <div class="w-100  w-md-50">
                        @include('components.desk-card', [
                            'icon' => 'bi-tools',
                            'title' => $services->title,
                            'count' => $services->count,
                            'viewRoute' => route('services.index'),
                            'createRoute' => route('services.create'),
                        ])
                    </div>

                    <div class="w-100  w-md-50">
                        @include('components.desk-card', [
                            'icon' => 'bi-ui-radios-grid',
                            'title' => $service_categories->title,
                            'count' => $service_categories->count,
                            'viewRoute' => route('service-categories.index'),
                            'createRoute' => route('service-categories.create'),
                        ])
                    </div>
                </div>
            </div>
        </div>

        <!-- Projects Card -->
        <div class="col-12 col-md-6 col-xl-4">
            @include('components.desk-card', [
                'icon' => 'bi-folder-plus',
                'title' => $projects->title,
                'count' => $projects->count,
                'viewRoute' => route('projects.index'),
                'createRoute' => route('projects.create'),
            ])
        </div>

        <!-- Partners Card -->
        <div class="col-12 col-md-6 col-xl-4">
            @include('components.desk-card', [
                'icon' => 'bi-people',
                'title' => $partners->title,
                'count' => $partners->count,
                'viewRoute' => route('partners.index'),
                'createRoute' => route('partners.create'),
            ])
        </div>

        <!-- Special Programs Card -->
        <div class="col-12 col-md-6 col-xl-4">
            @include('components.desk-card', [
                'icon' => 'bi-briefcase',
                'title' => $programs->title,
                'count' => $programs->count,
                'viewRoute' => route('programs.index'),
                'createRoute' => route('programs.create'),
            ])
        </div>

        <!-- Publications Card -->
        <div class="col-12 col-md-6 col-xl-4">
            @include('components.desk-card', [
                'icon' => 'bi-book',
                'title' => $publications->title,
                'count' => $publications->count,
                'viewRoute' => route('publications.index'),
                'createRoute' => route('publications.create'),
            ])
        </div>
    </div>
@endsection