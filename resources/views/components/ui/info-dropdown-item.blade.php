<li class="list-group-item  bg-transparent">
    <div class="d-flex justify-content-between flex-wrap align-items-center">
        <span>{{ $label }}:</span>
        @if ($items->isNotEmpty())
            <div class="position-relative d-inline-block" style="width: 50px; height: 25px;">
                <!-- Icon -->
                <div class="d-flex justify-content-center align-items-center gap-1">
                    <i class="{{ $icon }} fs-4 text-primary-emphasis"></i>
                    <i class="bi bi-caret-down-fill text-primary-emphasis"></i>
                </div>
                <!-- Transparent Select -->
                <select name="{{ $name }}" class="position-absolute top-0 start-0 opacity-0"
                    style="width: 100%; height: 100%;">
                    @foreach ($items as $item)
                        @if ($items->count() >= 2)
                            <option disabled>────</option>
                        @endif
                        <option disabled class="text-dark">{{ $getItemText($item) }}</option>
                    @endforeach
                </select>
            </div>
        @else
            <span class="badge bg-warning rounded-pill"><i class="bi bi-x-lg"></i></span>
        @endif
    </div>
</li>