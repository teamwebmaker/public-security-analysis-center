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
						@include('components.shared.table.partials.combined-actions-cell', [
							'hasCustomActions' => $hasCustomActions,
							'customActions' => $customActions,
							'hasModalTriggers' => $hasModalTriggers,
							'modalTriggers' => $modalTriggers,
							'model' => $model,
						])
					@endif

					<!-- Table default actions (pencil / delete) -->
					@if (isset($resourceName) && $actions === true)
						@include('components.shared.table.partials.default-actions-cell', [
							'resourceName' => $resourceName,
							'model' => $model,
							'action_delete' => $action_delete,
							'deleteMessage' => $deleteMessage,
						])
					@endif
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
