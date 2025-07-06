@props(['subscription'])
<!-- approve Button -->
@if (!$subscription->approved)
    <form method="POST" action="{{ route('push.approve', $subscription) }}" class="flex-grow-1">
        @csrf
        @method('PUT')
        <button type="submit"
            class="btn btn-outline-success btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-check2-circle"></i>
            <span>დამტკიცება</span>
        </button>
    </form>
@endif

<!-- reject Button -->
<form method="POST" action="{{ route('push.reject', $subscription) }}"
    onsubmit="return confirm('ნამდვილად გსურთ გამოწერა ნომერი {{ $subscription->id }} წაიშალოს?')" class="flex-grow-1">
    @csrf
    @method('DELETE')
    <button type="submit"
        class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
        <i class="bi bi-trash"></i>
        <span>უარყოფა</span>
    </button>
</form>