@extends('management.master')
@section('title', 'სამუშაოების მონაცემები')


@section('main')

	<!-- search -->
	<form method="GET" class="row row-cols-sm-auto justify-content-between align-items-center ">
		<div>
			<p class="fw-bold fs-5 m-0 text-center">ყველა სამუშაო</p>
		</div>
		<div class="row row-cols-1 row-cols-sm-auto g-4 m-0">
			<div class="col m-sm-0">
				<input name="filter[search]" value="{{ request('filter.search') }}" type="text" class="form-control w-100"
					placeholder="ძიება...">
				@if(request('filter.search'))
					<p class="text-muted m-0 align-self-start pt-2">საძიებო სიტყვა: <strong>{{ request('filter.search') }}</strong>
					</p>
				@endif
			</div>
			<div class="row mt-2 mt-sm-0 g-2 ">

				<div class="col m-sm-0">
					<button type="submit" class="btn btn-primary w-100">ძიება</button>
				</div>
				<div class="col m-sm-0 ">
					<a href="{{ route('management.dashboard.tasks') }}" class="btn btn-danger w-100">
						<i class="bi bi-trash-fill"></i>
					</a>
				</div>
			</div>

		</div>

	</form>
	<!-- Tasks -->
	<div class="my-3 shadow-sm rounded-3 overflow-hidden ">
		<x-shared.table :items="$tasks" :headers="['#', 'სტატუსი', 'შემსრულებელი', 'ფილიალი', 'სერვისი', 'საწყისი თარიღი', 'შექმნის თარიღი', 'განახლების თარიღი']" :rows="$userTableRows" :sortableMap="['საწყისი თარიღი' => 'start_date', 'შექმნის თარიღი' => 'created_at', 'განახლების თარიღი' => 'updated_at',]" :tooltipColumns="['branch', 'service']"
			:actions="false" />
	</div>

@endsection