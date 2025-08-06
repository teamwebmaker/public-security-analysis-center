@extends('layouts.admin.admin-dashboard')

@section('title', 'სამუშაოების სია')
@php
   $headers = ['#', 'სტატუსი', 'შემსრულებელი', 'ფილიალი', 'სერვისი', 'ხილვადობა', 'საწყისი თარიღი', 'შექმნის თარიღი', 'განახლების თარიღი'];
   $sortableMap = [
      'საწყისი თარიღი' => 'start_date',
      'შექმნის თარიღი' => 'created_at',
      'განახლების თარიღი' => 'updated_at',
   ];
@endphp

<x-admin.index-view :items="$tasks" :resourceName="$resourceName" containerClass="position-relative">
   <form method="GET" class="mb-3 row row-cols-1 row-cols-sm-auto g-2 justify-content-end">
      <div class="col">
         <input name="filter[search]" value="{{ request('filter.search') }}" type="text" class="form-control w-100"
            placeholder="ძიება...">
      </div>
      <div class="col">
         <button type="submit" class="btn btn-primary w-100">ძიება</button>
      </div>
      <div class="col">
         <a href="{{ route($resourceName . '.index') }}" class="btn btn-danger w-100">
            <i class="bi bi-trash-fill"></i>
         </a>
      </div>
   </form>
   @if(request('filter.search'))
      <p class="text-muted">საძიებო სიტყვა: <strong>{{ request('filter.search') }}</strong></p>
   @endif


   <x-shared.table :items="$tasks" :headers="$headers" :rows="$rows" :actions="true" :tooltipColumns="['branch', 'service']" :sortableMap="$sortableMap" :resourceName="$resourceName" />
</x-admin.index-view>