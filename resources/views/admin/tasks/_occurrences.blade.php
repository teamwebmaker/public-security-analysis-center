@props(['occurrences', 'occurrenceHeaders', 'occurrenceRows', 'task'])

@if($occurrences->isEmpty())
    <p class="text-muted mb-0">ციკლები ჯერ არ არის.</p>
@else
    <x-shared.table :items="$occurrences" :headers="$occurrenceHeaders" :rows="$occurrenceRows"
        :tooltipColumns="['branch', 'service']" />
    <div class="mt-3">
        {!! $occurrences->withQueryString()->links('pagination::bootstrap-5') !!}
    </div>
@endif
