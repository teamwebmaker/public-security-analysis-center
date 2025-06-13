@extends('layouts.dashboard')
@section('title', 'Admin Dashboard')
@section('main')
    <div class="row g-4">

        <!-- Projects Card -->
        <div class="col-12 col-md-6 col-xl-4">
            @include('components.dashboard-card', [
                'icon' => 'bi-folder-plus',
                'title' => $projects->title,
                'count' => $projects->count,
                'viewRoute' => route('projects.index'),
                'createRoute' => route('projects.index'),
            ])
        </div>

        <!-- Partners Card -->
        <div class="col-12 col-md-6 col-xl-4">
            @include('components.dashboard-card', [
                'icon' => 'bi-people',
                'title' => $partners->title,
                'count' => $partners->count,
                'viewRoute' => route('partners.index'),
                'createRoute' => route('partners.create'),
            ])
        </div>

        <!-- Special Programs Card -->
        <div class="col-12 col-md-6 col-xl-4">
            @include('components.dashboard-card', [
                'icon' => 'bi-briefcase',
                'title' => 'სპეც.პროგრამები',
                'count' => 40,
                'viewRoute' => route('programs.index'),
                'createRoute' => route('programs.create'),
            ])
        </div>

        <!-- Publications Card -->
        <div class="col-12 col-md-6 col-xl-4">
            @include('components.dashboard-card', [
                'icon' => 'bi-book',
                'title' => $publications->title,
                'count' => $publications->count,
                'viewRoute' => route('publications.index'),
                'createRoute' => route('publications.create'),
            ])
        </div>

    </div>
@endsection
