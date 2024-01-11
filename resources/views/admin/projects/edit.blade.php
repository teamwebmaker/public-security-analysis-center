@extends('layouts.dashboard')
@section('title', 'რედაქტირება/' .  $project -> title -> ka)
@section('main')
    @session('success')
    <div class="alert alert-success" role="alert"  x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        {{ $value }}
    </div>
    @endsession
    <div class="row">
        <div class="col-md-12">
            <form method="POST" enctype="multipart/form-data" action="{{ route('projects.update', ['project' => $project]) }}">
                @csrf
                @method('PUT')
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="ka-tab" data-bs-toggle="tab" data-bs-target="#ka-tab-content" type="button" role="tab" aria-controls="ka-tab-content" aria-selected="true">KA</button>
                        <button class="nav-link" id="en-tab" data-bs-toggle="tab" data-bs-target="#en-tab-content" type="button" role="tab" aria-controls="en-tab-content" aria-selected="false">EN</button>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade pt-2 show active" id="ka-tab-content" role="tabpanel" aria-labelledby="ka-tab" tabindex="0">
                        <div class="mb-3">
                            <input type="text" class="form-control" name="title_ka" placeholder="სათაური" value="{{ $project -> title -> ka }}" required/>
                        </div>
                        <div class="mb-3">
                            <textarea  class="form-control" rows="5" name="description_ka" placeholder="აღწერა" required> {{ $project -> description -> ka }}</textarea>
                        </div>
                    </div>
                    <div class="tab-pane pt-2 fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab" tabindex="0">
                        <div class="mb-3">
                            <input type="text" class="form-control" name="title_en" placeholder="Title" value="{{$project -> title -> en}}" required/>
                        </div>
                        <div class="mb-3">
                            <textarea  class="form-control" rows="5" name="description_en" placeholder="description" required> {{ $project -> description -> en}} </textarea>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <input type="file" class="form-control"  name="image" />
                </div>
                <div class="row justify-content-between">
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary">განახლება</button>
                    </div>
                    <div class="col-6">
                        <a href="{{ asset('images/projects/' . $project -> image) }}" data-fancybox data-caption="{{ $project -> title -> ka }}">
                            <img class="w-25 float-end rounded" src="{{ asset('images/projects/' . $project -> image) }}" />
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        Fancybox.bind('[data-fancybox]', {
            // Your custom options for a specific gallery
        });
    </script>
@endsection

