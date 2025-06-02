@extends('layouts.master')
@section('title',  'Programs Page')

@section('main')
    <main>
        <div class="container-fluid pt-5">
            <div class="container-xxl">
                <div class="row mb-5">
                    @foreach($programs as $program)
                        <div class="col-md-4  mb-4">
                            <x-program-component :program="$program" language="ka"/>
                        </div>
                    @endforeach
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {!! $programs->withQueryString()->links('pagination::bootstrap-5') !!}
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
