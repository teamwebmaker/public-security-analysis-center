@php
    $isAdmin = auth()->user()->isAdmin();
    $routePrefix = $isAdmin ? 'material-equipments' : 'management.material-equipments';
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <div>
        <h1 class="h4 mb-1">მატერიალურ-ტექნიკური აღჭ</h1>
        <p class="text-muted mb-0">ფილიალების აღჭურვილობის დოკუმენტები და არსებული ხელმოწერები</p>
    </div>

    @can('create', \App\Models\MaterialEquipment::class)
        <a href="{{ route("{$routePrefix}.create") }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            ჩანაწერის შექმნა
        </a>
    @endcan
</div>

<form method="GET" action="{{ route("{$routePrefix}.index") }}" class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-6 col-lg-4">
                <label for="material-equipment-search" class="form-label">ძებნა</label>
                <input id="material-equipment-search" type="search" name="search" class="form-control"
                    value="{{ request('search') }}" placeholder="დასახელება, კომპანია, ფილიალი ან ხელმომწერი">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>ძებნა
                </button>
            </div>
            @if (request()->filled('search'))
                <div class="col-auto">
                    <a href="{{ route("{$routePrefix}.index") }}" class="btn btn-outline-secondary">გასუფთავება</a>
                </div>
            @endif
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">დასახელება</th>
                        <th>ფილიალი</th>
                        <th>დოკუმენტი</th>
                        <th>ხელმომწერები</th>
                        <th>წვდომა</th>
                        <th class="text-end pe-3">მოქმედება</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($materialEquipments as $materialEquipment)
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route("{$routePrefix}.show", $materialEquipment) }}"
                                    class="fw-semibold text-decoration-none">
                                    {{ $materialEquipment->title }}
                                </a>
                            </td>
                            <td>
                                <div>{{ $materialEquipment->branch->name }}</div>
                                <small class="text-muted">{{ $materialEquipment->branch->company?->name }}</small>
                            </td>
                            <td>
                                @include('incidents.partials.document-preview-trigger', [
                                    'incident' => $materialEquipment,
                                    'previewRoute' => "{$routePrefix}.document",
                                    'downloadRoute' => "{$routePrefix}.document.download",
                                ])
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach ($materialEquipment->signatures as $signature)
                                        <span class="badge text-bg-success" title="{{ $signature->signed_at->format('d.m.Y H:i') }}">
                                            <i class="bi bi-check2 me-1"></i>{{ $signature->user->full_name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $materialEquipment->isPublic() ? 'text-bg-info' : 'text-bg-secondary' }}">
                                    {{ $materialEquipment->isPublic() ? 'საჯარო' : 'პირადი' }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                    <a href="{{ route("{$routePrefix}.show", $materialEquipment) }}"
                                        class="btn btn-sm btn-outline-primary">დეტალები</a>
                                    @can('update', $materialEquipment)
                                        <a href="{{ route("{$routePrefix}.edit", $materialEquipment) }}"
                                            class="btn btn-sm btn-outline-secondary">რედაქტირება</a>
                                    @endcan
                                    @can('delete', $materialEquipment)
                                        <form method="POST" action="{{ route("{$routePrefix}.destroy", $materialEquipment) }}"
                                            onsubmit="return confirm('ნამდვილად გსურთ ჩანაწერის წაშლა?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">წაშლა</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                მატერიალურ-ტექნიკური აღჭურვილობა ვერ მოიძებნა
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if ($materialEquipments->hasPages())
    <div class="mt-3">{{ $materialEquipments->links('pagination::bootstrap-5') }}</div>
@endif

@include('incidents.partials.document-preview-modal')
