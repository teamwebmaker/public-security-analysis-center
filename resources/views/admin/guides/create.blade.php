@extends('layouts.admin.admin-dashboard')

@section('title', 'გზამკვლევის შექმნა')

@section('main')
    <x-admin.crud.form-container method="POST" title="გზამკვლევის შექმნა"
        action="{{ route($resourceName . '.store') }}" :backRoute="$resourceName . '.index'">

        <div class="row">
            <div class="col-md-6 mb-3">
                <x-form.input name="name" label="სახელი" value="{{ old('name') }}" placeholder="შეიყვანეთ სახელი" />
            </div>
            <div class="col-md-6 mb-3">
                <x-form.input type="number" name="sort_order" label="რიგითობა" value="{{ old('sort_order') }}"
                    placeholder="შეიყვანეთ რიგითობა" min="1" />
            </div>
            <div class="col-md-12 mb-3">
                <x-form.input name="link" label="ბმული" value="{{ old('link') }}"
                    placeholder="მაგ: /admin/tasks ან https://example.com" />
            </div>
        </div>
    </x-admin.crud.form-container>
@endsection
