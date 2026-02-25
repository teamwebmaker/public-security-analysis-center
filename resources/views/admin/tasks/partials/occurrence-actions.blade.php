@php
    $isLatest = $isLatest ?? false;
@endphp

<div class="d-flex gap-2 align-items-center justify-content-center">
    @if ($occurrence->payment_status !== 'paid')
        <form method="POST" action="{{ route('task-occurrences.mark-paid', $occurrence) }}"
            onsubmit="return confirm('ნამდვილად გსურსთ ამ ციკლის გადახდის სტატუსი განაახლოთ როგორც გადახდილი?')" class="m-0">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-sm btn-outline-success" title="გადახდილია">
                <i class="bi bi-cash-coin"></i>
            </button>
        </form>
    @endif

    <a href="{{ route('task-occurrences.edit', $occurrence) }}" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-pencil-square"></i>
    </a>

    <form method="POST" action="{{ route('task-occurrences.destroy', $occurrence) }}"
        onsubmit="return confirm('წავშალოთ ეს ციკლი?')" class="m-0">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger{{ $isLatest ? ' disabled' : '' }}"
            @if ($isLatest) title="ბოლო ციკლი ვერ წაიშლება" @endif>
            <i class="bi bi-trash"></i>
        </button>
    </form>
</div>
