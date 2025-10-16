<div class="position-relative" style="width: 100%; overflow-x: auto; overflow-y: visible;">
	<table class="table table-hover align-middle mb-0" style="min-width: 1400px;">
		<!-- Table headers with sorting  -->
		<thead class="table-light text-uppercase">

			<tr>
				@foreach($headers as $header)
					@php
						$field = $sortableMap[$header] ?? null;
						$currentSort = request()->query('sort');
						$isSorted = ltrim($currentSort, '-') === $field;
						$dir = str_starts_with($currentSort, '-') ? 'desc' : 'asc';
						$next = $isSorted ? ($dir === 'asc' ? "-$field" : $field) : $field;
					@endphp

					<th class="{{ $loop->first ? 'text-center sticky-col' : '' }}">
						@if ($field)
							<a href="{{ request()->fullUrlWithQuery(['sort' => $next]) }}"
								class="text-dark text-decoration-none d-flex gap-1 align-items-center">
								<span>{{ $header }}</span>
								@if ($isSorted)
									<i class="bi bi-arrow-{{ $dir === 'asc' ? 'up' : 'down' }} text-primary"></i>
								@else
									<i class="bi bi-arrow-down-up"></i>
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

			<!-- Table rows -->
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
								data-bs-custom-class="custom-tooltip" data-bs-title="{{ strip_tags($value) }}" @endif
									style="max-width: 200px; display: inline-block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
									{!! $value !!}
								</span>
							</td>
						@endif
					@endforeach

					<!-- Table default actions -->
					@if (isset($resourceName) && $actions)
						<td class="text-end">
							<div class="dropdown dropstart">
								<button class="btn btn-sm btn-light border-0" data-bs-toggle="dropdown">
									<i class="bi bi-three-dots-vertical"></i>
								</button>
								<ul class="dropdown-menu">
									<!--  edit link -->
									<li>
										<a href="{{ route($resourceName . '.edit', $model) }}"
											class="dropdown-item text-primary d-flex align-items-center justify-center gap-2">
											<i class="bi bi-pencil-square"></i><span>რედაქტირება</span>
										</a>
									</li>
									<!--  delete action -->
									@if ($action_delete == true)
										<li>
											<form method="POST" action="{{ route($resourceName . '.destroy', $model) }}"
												onsubmit="return confirm('ნამდვილად გსურთ წაშლა?')">
												@csrf @method('DELETE')
												<button type="submit"
													class="dropdown-item text-danger d-flex align-items-center justify-center gap-2">
													<i class="bi bi-trash"></i><span>წაშლა</span>
												</button>
											</form>
										</li>
									@endif

								</ul>
							</div>
						</td>
					@endif

					<!--  custom actions -->
					@if (isset($customActions))
						@php
							$actions = is_callable($customActions) ? $customActions($model) : [];
						@endphp
						<td class="text-end">
							@foreach ($actions as $action)
								<form method="POST" action="{{ route($action['route_name'], $model) }}" @if (isset($action['confirm']))
								onsubmit="return confirm('{{ $action['confirm'] }}')" @endif>
									@csrf
									@if ($action['method'] !== 'POST')
										@method($action['method'])
									@endif

									<button type="submit"
										class="dropdown-item {{ $action['class'] ?? '' }} d-flex align-items-center justify-items-center gap-2">
										@if (!empty($action['icon']))
											<i class="bi {{ $action['icon'] }}"></i>
										@endif
										<span>{{ $action['label'] }}</span>
									</button>
								</form>
							@endforeach
						</td>
					@endif

					@if (isset($modalTriggers))
						@php
							$modals = is_callable($modalTriggers) ? $modalTriggers($model) : $modalTriggers;
						 @endphp

						<td class="text-end">
							@foreach ($modals as $modalTrigger)
								<a href="#" data-bs-toggle="modal" data-bs-target="#{{ $modalTrigger['modal_id'] }}"
									class="btn btn-light btn-sm px-3 rounded-pill d-inline-flex align-items-center gap-2 shadow-sm {{ $modalTrigger['class'] ?? '' }}">
									@if (!empty($modalTrigger['icon']))
										<i class="bi {{ $modalTrigger['icon'] }}"></i>
									@endif
									<span>{{ $modalTrigger['label'] }}</span>
								</a>
							@endforeach
						</td>
					@endif
				</tr>
			@endforeach
		</tbody>
	</table>
</div>