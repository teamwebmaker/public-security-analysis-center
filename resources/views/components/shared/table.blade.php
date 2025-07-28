{{-- <div style="width: 100%; overflow-x: auto;">
	<table class="table table-hover align-middle mb-0" style="min-width: 1300px;">
		<thead class="table-light text-uppercase">
			<tr>
				@foreach($headers as $header)
				<th scope="col" class="{{ $loop->first ? 'text-center sticky-col' : '' }}">
					{{ $header }}
				</th>
				@endforeach
			</tr>
		</thead>
		<tbody>
			@foreach($rows as $row)
			<tr>
				@foreach($row as $key => $value)
				@if ($loop->first)
				<th scope="row" class="text-nowrap text-dark fw-semibold text-center sticky-col">
					<div>{!! $value !!}</div>
				</th>
				@else
				<td>
					@php $hasTooltip = in_array($key, $tooltipColumns); @endphp
					<span @if($hasTooltip) class="text-truncate" data-bs-toggle="tooltip" data-bs-placement="top"
						data-bs-custom-class="custom-tooltip" data-bs-title="{{ strip_tags($value) }}" @endif
						style="max-width: 200px; cursor: pointer; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
						{!! $value !!}
					</span>
				</td>
				@endif
				@endforeach
			</tr>
			@endforeach
		</tbody>
	</table>
</div> --}}


<div class="position-relative" style="width: 100%; overflow-x: auto; overflow-y: visible;">
	<table class="table table-hover align-middle mb-0" style="min-width: 1300px;">
		<thead class="table-light text-uppercase">
			<tr>
				@foreach($headers as $header)
					<th scope="col" class="{{ $loop->first ? 'text-center sticky-col' : '' }}">
						{{ $header }}
					</th>
				@endforeach
				@if (!empty($actions) && $actions)
					<th scope="col" class="text-end"></th> {{-- No title for actions column --}}
				@endif
			</tr>
		</thead>
		<tbody>
			@foreach($rows as $index => $row)
				@php
					$document = $items[$index]; // Get the original model if needed
				@endphp
				<tr>
					@foreach($row as $key => $value)
						@if ($loop->first)
							<th scope="row" class="text-nowrap text-dark fw-semibold text-center sticky-col">
								<div>{!! $value !!}</div>
							</th>
						@else
							<td>
								@php $hasTooltip = in_array($key, $tooltipColumns); @endphp
								<span @if($hasTooltip) class="text-truncate" data-bs-toggle="tooltip" data-bs-placement="top"
								data-bs-custom-class="custom-tooltip" data-bs-title="{{ strip_tags($value) }}" @endif
									style="max-width: 200px; cursor: pointer; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
									{!! $value !!}
								</span>
							</td>
						@endif
					@endforeach

					@if (!empty($actions) && $actions)
						<td class="text-end">
							<div class="dropdown dropstart">
								<button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown"
									aria-expanded="false">
									<i class="bi bi-three-dots-vertical"></i>
								</button>
								<ul class="dropdown-menu">
									<li>
										<a href="{{ route($resourceName . '.edit', $document) }}"
											class="dropdown-item bg-transparent d-flex align-items-center gap-2 text-primary">
											<i class="bi bi-pencil-square"></i>
											<span>რედაქტირება</span>
										</a>
									</li>
									<li>
										<form method="POST" action="{{ route($resourceName . '.destroy', $document) }}"
											onsubmit="return confirm('ნამდვილად გსურთ სამუშაო {{ $document->service->title->ka ?? '' }} წაიშალოს?')">
											@csrf
											@method('DELETE')
											<button type="submit"
												class="dropdown-item bg-transparent d-flex align-items-center gap-2 text-danger">
												<i class="bi bi-trash"></i>
												<span>წაშლა</span>
											</button>
										</form>
									</li>
								</ul>
							</div>
						</td>
					@endif
				</tr>
			@endforeach
		</tbody>
	</table>
</div>