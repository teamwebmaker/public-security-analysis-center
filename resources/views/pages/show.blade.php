@extends('layouts.master')
@section('title', $item->title->$language)
@section('main')
    <div class="container-fluid my-4">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <x-single-item-component :item="$item" :language="$language" :category="$category"
                        :isPdfMarkerDisplayed="isset($item->file)" />
                </div>
            </div>
        </div>
    </div>
@endsection