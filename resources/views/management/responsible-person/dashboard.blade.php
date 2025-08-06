@extends('management.master')
@section('title', 'ფილიალის მონაცემები')

@section('main')
   <!-- Stats -->
   <div class="row g-3">
      <x-management.stat-card label="აქტიური სამუშაოები" :count="$inProgressTasks->count()" icon="bi bi-ui-radios"
        iconWrapperClasses="bg-warning bg-opacity-10 text-warning" />

      <x-management.stat-card label="ფილიალები" :count="$userBranches->count()" icon="bi bi-diagram-3-fill"
        iconWrapperClasses="bg-success bg-opacity-10 text-success" />
   </div>


   <!-- Tasks -->
   <div class="my-4">
      <p class="fw-bold fs-5">აქტიური სამუშაოები</p>
      <div class="my-3 shadow-sm rounded-3 overflow-hidden ">
        <x-shared.table :items="$inProgressTasks" :headers="['#', 'სტატუსი', 'შემსრულებელი', 'ფილიალი', 'სერვისი', 'საწყისი თარიღი', 'შექმნის თარიღი', 'განახლების თარიღი']" :rows="$userTableRows" :sortableMap="['საწყისი თარიღი' => 'start_date', 'შექმნის თარიღი' => 'created_at', 'განახლების თარიღი' => 'updated_at',]"
          :tooltipColumns="['branch', 'service']" :actions="false" />
      </div>
   </div>

   <!-- Branches -->
   <div class="my-4">
      <p class="fw-bold fs-5">ფილიალები</p>
      <div class="my-3 shadow-sm rounded-3 overflow-hidden ">
        <x-shared.table :items="$userBranches" :headers="['#', 'სახელი', 'მისამართი', 'მშობელი კომპანია', 'შექმნის თარიღი']" :rows="$branchTableRows" :sortableMap="['შექმნის თარიღი' => 'created_at']" :tooltipColumns="['name', 'address', 'company']" :actions="false" />
      </div>
   </div>
@endsection