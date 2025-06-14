@extends('layouts.master')
@section('title', 'Projects Page')

@section('main')
    <main>
        <div class="container-fluid pt-5">
            <div class="container-xxl">
                <div class="row mb-5">
                    @if ($projects->isEmpty())
                        <div class="col-md-8">
                            <div class="alert alert-info">
                                {{-- {{ __(' No projects found') }} --}}
                                No projects found
                            </div>
                        </div>
                    @else
                        @foreach($projects as $project)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <x-project-component :project="$project" language="ka" />
                            </div>
                        @endforeach
                    @endif
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