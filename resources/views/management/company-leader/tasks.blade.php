@extends('management.company-leader.master')
@section('title', 'სამუშაოების მონაცემები')


@section('main')



   <form method="GET" class="row row-cols-sm-auto justify-content-end  g-2 ">
      <div class="">
        <input name="filter[search]" value="{{ request('filter.search') }}" type="text" class="form-control w-100"
          placeholder="ძიება...">
        @if(request('filter.search'))
         <p class="text-muted m-0 align-self-start pt-2">საძიებო სიტყვა: <strong>{{ request('filter.search') }}</strong>
         </p>
       @endif
      </div>
      <div class="col">
        <button type="submit" class="btn btn-primary w-100">ძიება</button>
      </div>
      <div class="col">
        <a href="{{ route('management.dashboard.tasks') }}" class="btn btn-danger w-100">
          <i class="bi bi-trash-fill"></i>
        </a>
      </div>
   </form>


   <!-- Tasks -->
   <div class="my-3 shadow-sm rounded-3 overflow-hidden ">
      <x-shared.table :items="$tasks" :headers="['#', 'სტატუსი', 'შემსრულებელი', 'ფილიალი', 'სერვისი', 'საწყისი თარიღი', 'შექმნის თარიღი', 'განახლების თარიღი']" :rows="$userTableRows" :sortableMap="['საწყისი თარიღი' => 'start_date', 'შექმნის თარიღი' => 'created_at', 'განახლების თარიღი' => 'updated_at',]" :tooltipColumns="['branch', 'service']"
        :actions="false" />
   </div>

@endsection