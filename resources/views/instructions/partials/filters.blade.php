<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ $action }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-6 col-xl-4">
                    <label for="instruction-search" class="form-label">ძებნა</label>
                    <input id="instruction-search" type="search" name="search" class="form-control"
                        value="{{ request('search') }}"
                        placeholder="{{ ($showAdminFilters ?? false) ? 'სახელი, ვიდეო ან შემსრულებელი' : 'სახელი ან ვიდეო' }}">
                </div>

                @if ($showAdminFilters ?? false)
                    <div class="col-md-6 col-xl-3">
                        <label for="instruction-worker-filter" class="form-label">შემსრულებელი</label>
                        <select id="instruction-worker-filter" name="worker_id" class="form-select">
                            <option value="">ყველა შემსრულებელი</option>
                            @foreach ($workers as $worker)
                                <option value="{{ $worker->id }}" @selected((string) request('worker_id') === (string) $worker->id)>
                                    {{ $worker->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 col-xl-2">
                        <label for="instruction-visibility-filter" class="form-label">ხილვადობა</label>
                        <select id="instruction-visibility-filter" name="visibility" class="form-select">
                            <option value="">ყველა</option>
                            <option value="1" @selected((string) request('visibility') === '1')>ხილული</option>
                            <option value="0" @selected((string) request('visibility') === '0')>დამალული</option>
                        </select>
                    </div>
                @endif

                <div class="col-md-6 col-xl-2">
                    <label for="instruction-sharing-filter" class="form-label">გაზიარება</label>
                    <select id="instruction-sharing-filter" name="document_visibility" class="form-select">
                        <option value="">ყველა</option>
                        <option value="public" @selected(request('document_visibility') === 'public')>საჯარო</option>
                        <option value="private" @selected(request('document_visibility') === 'private')>პირადი</option>
                    </select>
                </div>

                <div class="col-md-6 col-xl-2">
                    <label for="instruction-type-filter" class="form-label">დოკუმენტის ტიპი</label>
                    <select id="instruction-type-filter" name="document_type" class="form-select">
                        <option value="">ყველა ტიპი</option>
                        <option value="pdf" @selected(request('document_type') === 'pdf')>PDF</option>
                        <option value="word" @selected(request('document_type') === 'word')>Word</option>
                        <option value="excel" @selected(request('document_type') === 'excel')>Excel</option>
                    </select>
                </div>

                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i>გაფილტვრა
                    </button>
                    <a href="{{ $action }}" class="btn btn-outline-secondary">გასუფთავება</a>
                </div>
            </div>
        </form>
    </div>
</div>
