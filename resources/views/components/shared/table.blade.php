 <div class="position-relative" style="width: 100%; overflow-x: auto; overflow-y: visible;">
	<table class="table table-hover align-middle mb-0" style="min-width: 1400px;">
		<thead class="table-light text-uppercase">
			<tr>
				@foreach($headers as $header)
					@php
						$field = $sortableMap[$header] ?? null;
						$isSorted = ltrim($currentSort, '-') === $field;
						$dir = str_starts_with($currentSort, '-') ? 'desc' : 'asc';
						$next = $isSorted && $dir === 'asc' ? "-$field" : $field;
					@endphp

					<th class="{{ $loop->first ? 'text-center sticky-col' : '' }}">
						@if ($field)
							<a href="{{ request()->fullUrlWithQuery(['sort' => $next]) }}" class="text-dark text-decoration-none d-flex gap-1 align-items-center">
								<span>{{ $header }}</span>
								@if ($isSorted)
									<i class="bi bi-caret-{{ $dir === 'asc' ? 'up' : 'down' }}-fill"></i>
								@endif
							</a>
						@else
							{{ $header }}
						@endif
					</th>
				@endforeach

				@if ($actions ?? false)
					<th class="text-end"></th>
				@endif
			</tr>
		</thead>
		<tbody>
			@foreach($rows as $i => $row)
				@php $model = $items[$i]; @endphp
				<tr>
					@foreach($row as $key => $value)
						@if ($loop->first)
							<th class="text-center sticky-col text-dark fw-semibold">{!! $value !!}</th>
						@else
							<td>
								@php $tooltip = in_array($key, $tooltipColumns ?? []); @endphp
								<span @if($tooltip) class="text-truncate d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top"
            					data-bs-custom-class="custom-tooltip" data-bs-title="{{ strip_tags($value) }}"@endif
									style="max-width: 200px; display: inline-block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
									{!! $value !!}
								</span>
							</td>
						@endif
					@endforeach

					@if ($actions ?? false)
						<td class="text-end">
							<div class="dropdown dropstart">
								<button class="btn btn-sm btn-light border-0" data-bs-toggle="dropdown">
									<i class="bi bi-three-dots-vertical"></i>
								</button>
								<ul class="dropdown-menu">
									<li>
										<a href="{{ route($resourceName . '.edit', $model) }}" class="dropdown-item text-primary d-flex gap-2">
											<i class="bi bi-pencil-square"></i><span>რედაქტირება</span>
										</a>
									</li>
									<li>
										<form method="POST" action="{{ route($resourceName . '.destroy', $model) }}" onsubmit="return confirm('ნამდვილად გსურთ წაშლა?')">
											@csrf @method('DELETE')
											<button type="submit" class="dropdown-item text-danger d-flex gap-2">
												<i class="bi bi-trash"></i><span>წაშლა</span>
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
