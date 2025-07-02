@extends('layouts.dashboard')
@section('title', 'Admin Dashboard')
@section('main')
    <div class="row align-items-center g-4">

        <!-- Grouped Service + Category Card -->
        <div class="col-12">
            <div class="card border border-white shadow-sm bg-light rounded-4 h-100 p-3">

                <!-- Optional title -->
                <h6 class="text-muted mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-layers text-secondary"></i>
                    სერვისები და კატეგორიები
                </h6>

                <!-- Side-by-side equal-width cards -->
                <div class="d-flex flex-column flex-md-row gap-3">
                    <div class="w-100 w-md-50">
                        @include('components.desk-card', [
                            'icon' => 'bi-tools',
                            'title' => $services->title,
                            'count' => $services->count,
                            'viewRoute' => route($services->resourceName . '.index'),
                            'createRoute' => route($services->resourceName . '.create'),
                        ])
                    </div>

                    <div class="w-100 w-md-50">
                        @include('components.desk-card', [
                            'icon' => 'bi-ui-radios-grid',
                            'title' => $service_categories->title,
                            'count' => $service_categories->count,
                            'viewRoute' => route($service_categories->resourceName . '.index'),
                            'createRoute' => route($service_categories->resourceName . '.create'),
                        ])
                    </div>
                </div>
            </div>
        </div>

        <!-- Grouped Programs, Syllabuses, Mentors -->
        <div class="col-12">
            <div class="card border border-white shadow-sm bg-light rounded-4 h-100 p-3">

                <!-- Optional title -->
                <h6 class="text-muted mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-layers text-secondary"></i>
                    სპეც პროგრამები და სილაბუსები
                </h6>

                <!-- Side-by-side equal-width cards -->
                <div class="d-flex flex-wrap flex-column flex-md-row gap-3">
                    <div class="col">
                        @include('components.desk-card', [
                            'icon' => 'bi-briefcase',
                            'title' => $programs->title,
                            'count' => $programs->count,
                            'viewRoute' => route($programs->resourceName . '.index'),
                            'createRoute' => route($programs->resourceName . '.create'),
                        ])
                    </div>
                    <div class="col">
                        @include('components.desk-card', [
                            'icon' => 'bi-file-earmark-text',
                            'title' => $syllabuses->title,
                            'count' => $syllabuses->count,
                            'viewRoute' => route($syllabuses->resourceName . '.index'),
                            'createRoute' => route($syllabuses->resourceName . '.create'),
                        ])
                    </div>
                    <div class="col">
                        @include('components.desk-card', [
                            'icon' => 'bi-person-lines-fill',
                            'title' => $mentors->title,
                            'count' => $mentors->count,
                            'viewRoute' => route($mentors->resourceName . '.index'),
                            'createRoute' => route($mentors->resourceName . '.create'),
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
                'viewRoute' => route($projects->resourceName . '.index'),
                'createRoute' => route($projects->resourceName . '.create'),
            ])
        </div>

        <!-- Partners Card -->
        <div class="col-12 col-md-6 col-xl-4">
            @include('components.desk-card', [
                'icon' => 'bi-people',
                'title' => $partners->title,
                'count' => $partners->count,
                'viewRoute' => route($partners->resourceName . '.index'),
                'createRoute' => route($partners->resourceName . '.create'),
            ])
        </div>

        <!-- Publications Card -->
        <div class="col-12 col-md-6 col-xl-4">
            @include('components.desk-card', [
                'icon' => 'bi-book',
                'title' => $publications->title,
                'count' => $publications->count,
                'viewRoute' => route($publications->resourceName . '.index'),
                'createRoute' => route($publications->resourceName . '.create'),
            ])
        </div>

        <!-- infos Card -->
        <div class="col-12 col-md-6 col-xl-4">
            @include('components.desk-card', [
                'icon' => 'bi-book',
                'title' => $infos->title,
                'count' => $infos->count,
                'viewRoute' => route($infos->resourceName . '.index'),
                // 'createRoute' => route($infos->resourceName . '.create'),
            ])
        </div>
        <!-- main_menus Card -->
        <div class="col-12 col-md-6 col-xl-4">
            @include('components.desk-card', [
                'icon' => 'bi-book',
                'title' => $main_menus->title,
                'count' => $main_menus->count,
                'viewRoute' => route($main_menus->resourceName . '.index'),
                'createRoute' => route($main_menus->resourceName . '.create'),
            ])
        </div>

        <!-- Contacts Card -->
        {{-- <div class="col-12 col-md-6 col-xl-4">
            @include('components.desk-card', [
                'icon' => 'bi-envelope',
                'title' => $contacts->title,
                'count' => $contacts->count,
                'viewRoute' => route($contacts->resourceName . '.index'),
                'createRoute' => route($contacts->resourceName . '.create'),
            ])
        </div> --}}

    </div>
@endsection
