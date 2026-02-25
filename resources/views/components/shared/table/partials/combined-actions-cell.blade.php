@php
    $actionsList = $hasCustomActions ? (is_callable($customActions) ? $customActions($model) : $customActions) : [];
    $modals = $hasModalTriggers ? (is_callable($modalTriggers) ? $modalTriggers($model) : $modalTriggers) : [];
@endphp

<td class="text-end">
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
            <form method="POST" class="m-0" action="{{ route($action['route_name'], $model) }}"
                @if (isset($action['confirm'])) onsubmit="return confirm('{{ $action['confirm'] }}')" @endif>
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
