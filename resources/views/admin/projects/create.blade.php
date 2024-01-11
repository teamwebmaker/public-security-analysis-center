@extends('layouts.dashboard')
@section('title', 'პროექტის შექმნა')
@section('main')
<div class="row">
        <div class="col-md-12">
            <form method="POST"  action="{{ route('projects.store') }}" enctype="multipart/form-data">
                @csrf
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="ka-tab" data-bs-toggle="tab" data-bs-target="#ka-tab-content" type="button" role="tab" aria-controls="ka-tab-content" aria-selected="true">KA</button>
                        <button class="nav-link" id="en-tab" data-bs-toggle="tab" data-bs-target="#en-tab-content" type="button" role="tab" aria-controls="en-tab-content" aria-selected="false">EN</button>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade pt-2 show active" id="ka-tab-content" role="tabpanel" aria-labelledby="ka-tab" tabindex="0">
                        <div class="mb-3">
                            <input type="text" class="form-control" name="title_ka" placeholder="სათაური" required/>
                        </div>
                        <div class="mb-3">
                            <textarea  class="form-control" rows="5" name="description_ka" placeholder="აღწერა" required></textarea>
                        </div>
                    </div>
                    <div class="tab-pane pt-2 fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab" tabindex="0">
                        <div class="mb-3">
                             <input type="text" class="form-control" name="title_en" placeholder="Title" required/>
                        </div>
                       <div class="mb-3">
                           <textarea  class="form-control" rows="5" name="description_en" placeholder="description" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <input type="file" class="form-control"  name="image" required/>
                </div>
                <button type="submit" class="btn btn-primary">დამატება</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script></script>
@endsection

