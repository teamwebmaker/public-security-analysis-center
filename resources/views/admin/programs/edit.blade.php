@extends('layouts.dashboard')
@section('title', 'პროგრამის რედაქტირება')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <form method="POST" action="{{ route('programs.store') }}" enctype="multipart/form-data" id="program-form">
                @csrf

                {{-- Multilingual Tabs --}}
                <nav>
                    <div class="nav nav-tabs" id="lang-tabs" role="tablist">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#ka" type="button">KA</button>
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#en" type="button">EN</button>
                    </div>
                </nav>
                <div class="tab-content mt-3">
                    {{-- KA --}}
                    <div class="tab-pane fade show active" id="ka">
                        <div class="mb-3">
                            <input type="text" name="title[ka]" class="form-control" placeholder="სათაური (ka)">
                        </div>
                        <div class="mb-3">
                            <textarea name="description[ka]" class="form-control" rows="4"
                                placeholder="აღწერა (ka)"></textarea>
                        </div>
                    </div>
                    {{-- EN --}}
                    <div class="tab-pane fade" id="en">
                        <div class="mb-3">
                            <input type="text" name="title[en]" class="form-control" placeholder="Title (en)">
                        </div>
                        <div class="mb-3">
                            <textarea name="description[en]" class="form-control" rows="4"
                                placeholder="Description (en)"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Image Uploads --}}
                <div class="mb-3">
                    <label>Program Image</label>
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Certificate Image</label>
                    <input type="file" name="certificate_image" class="form-control">
                </div>

                {{-- Video, Price, Duration --}}
                <div class="mb-3">
                    <input type="text" name="video" class="form-control" placeholder="Video Link (YouTube, Vimeo, etc.)">
                </div>
                <div class="mb-3">
                    <input type="number" name="price" class="form-control" placeholder="ფასი (₾)">
                </div>
                <div class="mb-3">
                    <input type="text" name="duration" class="form-control" placeholder="ხანგრძლივობა (eg. 2 კვირა)">
                </div>

                {{-- Dates and Time --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <input type="date" name="start_date" class="form-control" placeholder="საწყისი თარიღი">
                    </div>
                    <div class="col-md-6 mb-3">
                        <input type="date" name="end_date" class="form-control" placeholder="დასასრული თარიღი">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>დასაწყისი დრო</label>
                        <input type="time" name="hour[start]" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>დასრულება</label>
                        <input type="time" name="hour[end]" class="form-control">
                    </div>
                </div>

                {{-- Days (Multiple Select) --}}
                <div class="mb-3">
                    <label>კვირის დღეები</label>
                    <select name="days[]" class="form-select" multiple>
                        <option value="ორშაბათი">ორშაბათი</option>
                        <option value="სამშაბათი">სამშაბათი</option>
                        <option value="ოთხშაბათი">ოთხშაბათი</option>
                        <option value="ხუთშაბათი">ხუთშაბათი</option>
                        <option value="პარასკევი">პარასკევი</option>
                        <option value="შაბათი">შაბათი</option>
                        <option value="კვირა">კვირა</option>
                    </select>
                </div>

                {{-- Address --}}
                <div class="mb-3">
                    <input type="text" name="address" class="form-control" placeholder="მისამართი">
                </div>

                {{-- Visibility & Sortable --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>ხილვადობა</label>
                        <select name="visibility" class="form-select">
                            <option value="1">ხილული</option>
                            <option value="0">დამალული</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="number" name="sortable" class="form-control mt-4" placeholder="დალაგების მიმდევრობა">
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary">შენახვა</button>
                </div>
            </form>
        </div>
    </div>
@endsection