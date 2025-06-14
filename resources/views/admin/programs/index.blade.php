@extends('layouts.dashboard')
@section('title', 'პროგრამების სია')

@section('main')
	@session('success')
		<div class="alert alert-success alert-dismissible fade show" role="alert" x-data="{ show: true }" x-show="show"
			x-init="setTimeout(() => show = false, 3000)">
			{{ $value }}
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	@endsession

	<div class="row g-4 ">
		@foreach($programs as $program)
			<div class="col-12 col-sm-7 col-md-6 ">
				<div class="card shadow-sm">
					@if ($program->image)
						<div class="card-header p-0 overflow-hidden overlay-label-wrapper" data-label="სურათის ნახვა" data-alpha="0.7"
							style="--overlay-alpha: 0.7; height: 200px;">
							<a href="{{ asset('images/programs/' . $program->image) }}" data-fancybox
								data-caption="{{ $program->title->en }}">
								<img class="w-100 h-100 object-fit-cover" src="{{ asset('images/programs/' . $program->image) }}"
									alt="{{ $program->title->ka }}">
							</a>
						</div>
					@else
						<div class="card-header p-0 overflow-hidden overlay-label-wrapper" data-label="სურათის ნახვა" data-alpha="0.7"
							style="--overlay-alpha: 0.7; height: 170px;">
							<a href="{{ asset('images/programs/not-found-image.webp') }}" data-fancybox
								data-caption="სურათი არ არის ატვირთული">
								<img class="w-100 h-100 object-fit-cover" src="{{ asset('images/programs/not-found-image.webp') }}"
									alt="Not found image">
							</a>
						</div>
					@endif
					<div class="card-body">
						<h5 class="card-title text-truncate" title="{{ $program->title->ka }}">
							{{ $program->title->ka }}
						</h5>
						<h6 class="card-subtitle mb-2 text-muted small">{{ $program->title->en }}</h6>

						<div x-data="{ expanded: false, height: 0 }" x-init="$nextTick(() => height = $refs.content.scrollHeight)">

							<!-- Description with smooth transition -->
							<div @click="expanded = !expanded" class="overflow-hidden transition-all  duration-500 ease-in-out mb-1 "
								:style="expanded ? 'max-height:' + height + 'px' : 'max-height: 4.5em;'">
								<p class="card-text mb-0" x-ref="content" style="cursor: pointer;">
									{{ $program->description->ka }}
								</p>
							</div>

							<!-- Toggle button stays outside to avoid clipping -->
							@if (strlen($program->description->ka) > 100)
								<button @click="expanded = !expanded" class="btn btn-sm btn-link p-0 text-decoration-none">
									<i class="bi me-1" :class="expanded ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
									<span x-text="expanded ? 'ნაკლების ჩვენება' : 'მეტის ჩვენება'"></span>
								</button>
							@endif

						</div>
						<ul class="list-group list-group-flush mb-3">
							<li class="list-group-item d-flex justify-content-between align-items-center">
								<span>ფასი:</span>
								<span class="badge bg-primary rounded-pill" style="font-size: 13px;">{{ $program->price }}
									₾</span>
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-center">
								<span>ხანგრძლივობა:</span>
								<span>{{ $program->duration }}</span>
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-center">
								<span>საათები:</span>
								<span>{{ $program->hour->start }} - {{ $program->hour->end }}</span>
							</li>
							<li class="list-group-item">
								<div class="d-flex justify-content-between">
									<span>დღეები:</span>
									<select>
										<option class="badge bg-secondary me-1"> <span>სულ</span>
											{{ count($program->days->ka ?? [])}}
										</option>
										@foreach($program->days->ka ?? [] as $day)
											<option class="badge bg-secondary me-1" disabled>{{ $day }}</option>
										@endforeach
									</select>
								</div>
							</li>
							<li class="list-group-item">
								<div class="d-flex justify-content-between">
									<span>ლოკაცია:</span>
									<span class="text-end">{{ $program->address }}</span>
								</div>
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-center">
								<span>სტატუსი:</span>
								<span class="badge {{ $program->visibility ? 'bg-success' : 'bg-warning' }}">
									{{ $program->visibility ? 'ხილული' : 'დამალული' }}
								</span>
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-center">
								<span>დაწყება:</span>
								<span>{{ $program->start_date }}</span>
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-center">
								<span>დასრულება:</span>
								<span>{{ $program->end_date }}</span>
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-center">
								<span>ლოკაცია:</span>
								<span class="text-truncate d-inline-block" style="max-width: 150px; cursor: pointer;"
									data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $program->address }}">
									{{ $program->address }}
								</span>
							</li>
							@if ($program->video)
								<li class="list-group-item">
									<a href="{{ $program->video }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
										<i class="bi bi-play-circle me-2"></i>ვიდეოს ნახვა
									</a>
								</li>
							@endif
							@if ($program->certificate_image)
								<li class="list-group-item">
									<a href="{{ asset('images/certificates/programs/' . $program->certificate_image) }}" data-fancybox
										class="btn btn-sm btn-outline-success w-100">
										<i class="bi bi-award me-2"></i>სერტიფიკატის ნახვა
									</a>
								</li>
							@endif
						</ul>
					</div>
					<div class="card-footer bg-transparent ">
						<div class="d-grid gap-2 d-md-flex justify-content-md-end">
							<a class="btn btn-outline-primary me-md-2" href="{{ route('programs.edit', ['program' => $program]) }}">
								<i class="bi bi-pencil-square me-1"></i>რედაქტირება
							</a>
							<form method="POST" action="{{ route('programs.destroy', ['program' => $program]) }}"
								onsubmit="return confirm('წავშალოთ კურსი?')">
								@csrf
								@method('DELETE')
								<button type="submit" class="btn btn-outline-danger w-100">
									<i class="bi bi-trash me-1"></i>წაშლა
								</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		@endforeach
	</div>

	<div class="row mt-4">
		<div class="col-md-12">
			<nav aria-label="Page navigation">
				{!! $programs->withQueryString()->links('pagination::bootstrap-5') !!}
			</nav>
		</div>
	</div>
@endsection

@section('scripts')
	<script>
		Fancybox.bind('[data-fancybox]', {});
	</script>
@endsection