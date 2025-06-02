@extends('layouts.master')
@section('title',  'Publications Page')


@section('main')
    <main>
        <div class="container-fluid pt-5">
            <div class="container-xxl">
                <div class="row mb-5">
                    @foreach($publications as $publication)
                        <div class="col-md-4  mb-4">
                            <x-publication-component :publication="$publication" language="ka"/>
                        </div>
                    @endforeach
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {!! $publications->withQueryString()->links('pagination::bootstrap-5') !!}
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
