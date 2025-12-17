<div class="position-relative" style="width: 100%; overflow-x: auto; overflow-y: visible;">
	<table class="table table-hover align-middle mb-0" style="min-width: 1400px;">
		<!-- Table headers with sorting  -->
		<thead class="table-light text-uppercase">
			@php
				$hasCustomActions = isset($customActions);
				$hasModalTriggers = isset($modalTriggers);
				$hasDefaultActions = isset($resourceName) && ($actions ?? false);
			@endphp
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

				@if($hasCustomActions || $hasModalTriggers)
					<th class="text-end text-nowrap">ქმედებები</th>
				@endif

				@if ($hasDefaultActions)
					<th class="text-end"><i class="bi bi-pencil-square"></i></th>
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

					<!-- Combined custom actions / modal triggers -->
					@if ($hasCustomActions || $hasModalTriggers)
						<td class="text-end">
							@php
								$actionsList = $hasCustomActions ? (is_callable($customActions) ? $customActions($model) : $customActions) : [];
								$modals = $hasModalTriggers ? (is_callable($modalTriggers) ? $modalTriggers($model) : $modalTriggers) : [];
							@endphp

							<div class="d-flex flex-wrap gap-2 justify-content-end align-items-center">
								@foreach ($modals as $modalTrigger)
									<a href="#" data-bs-toggle="modal" data-bs-target="#{{ $modalTrigger['modal_id'] }}"
										class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-2 {{ $modalTrigger['class'] ?? '' }}">
										@if (!empty($modalTrigger['icon']))
											<i class="bi {{ $modalTrigger['icon'] }}"></i>
										@endif
										<span>{{ $modalTrigger['label'] }}</span>
									</a>
								@endforeach

								@foreach ($actionsList as $action)
									<form method="POST" action="{{ route($action['route_name'], $model) }}" @if (isset($action['confirm'])) onsubmit="return confirm('{{ $action['confirm'] }}')" @endif>
										@csrf
										@if (($action['method'] ?? 'POST') !== 'POST')
											@method($action['method'])
										@endif

										<button type="submit"
											class="btn btn-sm {{ $action['class'] ?? 'btn-outline-secondary' }} d-inline-flex align-items-center gap-2">
											@if (!empty($action['icon']))
												<i class="bi {{ $action['icon'] }}"></i>
											@endif
											<span>{{ $action['label'] }}</span>
										</button>
									</form>
								@endforeach

								@if (empty($actionsList) && empty($modals))
									<div class="text-muted">---</div>
								@endif
							</div>
						</td>
					@endif

					<!-- Table default actions (pencil / delete) -->
					@if (isset($resourceName) && $actions === true)
						<td class="text-end">
							<div class="dropdown dropstart">
								<button class="btn btn-sm btn-light border-0" data-bs-toggle="dropdown">
									<i class="bi bi-three-dots-vertical"></i>
								</button>
								<ul class="dropdown-menu dropdown-menu-end">
									<!--  edit link -->
									<li>
										<a href="{{ route($resourceName . '.edit', $model) }}"
											class="dropdown-item text-primary d-flex align-items-center justify-center gap-2">
											<i class="bi bi-pencil-square"></i><span>რედაქტირება</span>
										</a>
									</li>
									<!--  delete action -->
									@if ($action_delete == true)
										<li class="m-0">
											<form class="m-0" method="POST" action="{{ route($resourceName . '.destroy', $model) }}"
												onsubmit="return confirm('{{ $deleteMessage }}')">
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
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
