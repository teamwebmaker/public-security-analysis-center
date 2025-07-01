@extends('layouts.master')
@section('title', 'Projects Page')

@section('main')
    <main>
        <div class="container-fluid pt-2">
            <div class="container-xxl">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between py-4 ">
                    <h2 class="mb-3 mb-md-0">პროექტები</h2>

                    <!-- Fixed Filter Component -->
                    @if ($projects->isNotEmpty())
                        <x-sort-data name="sort" :selected="request()->query('sort', 'newest')" class="" />
                    @endif
                </div>
                <div class="row mb-5">
                    @forelse ($projects as $project)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <x-card-component :title="$project->title->$language"
                                :description="$project->description->$language" :image="'images/projects/' . $project->image"
                                :link="route('projects.show', ['id' => $project->id])" />
                        </div>
                    @empty
                        {{-- {{ __('No projects found') }} --}}
                        <x-ui.empty-state-message :message="'პროექტები ვერ მოიძებნა'" />
                    @endforelse

                </div>
                <div class="row">
                    <div class="col-md-12">
                        {!! $projects->withQueryString()->links('pagination::bootstrap-5') !!}
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection