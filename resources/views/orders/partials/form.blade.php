@php
    $isEdit = isset($order) && $order !== null;
    $selectedUsers = collect(old(
        'user_participant_ids',
        $isEdit ? $order->userParticipants->pluck('user_id')->all() : []
    ))->map(fn($id) => (int) $id)->values();
@endphp

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-4">{{ $formTitle }}</h1>

                <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data">
                    @csrf
                    @if ($formMethod !== 'POST')
                        @method($formMethod)
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="order-title" class="form-label">დასახელება <span class="text-danger">*</span></label>
                            <input id="order-title" type="text" name="title" class="form-control"
                                value="{{ old('title', $order?->title) }}" maxlength="255" required>
                            @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="order-branch" class="form-label">ფილიალი <span class="text-danger">*</span></label>
                            <select id="order-branch" name="branch_id" class="form-select" required
                                @if($isEdit) disabled @endif>
                                <option value="">აირჩიეთ ფილიალი</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        @selected((string) old('branch_id', $order?->branch_id) === (string) $branch->id)>
                                        {{ $branch->name }} · {{ $branch->company?->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($isEdit)
                                <input type="hidden" name="branch_id" value="{{ $order->branch_id }}">
                            @endif
                            @error('branch_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="order-document" class="form-label">
                                დოკუმენტი @unless($isEdit)<span class="text-danger">*</span>@endunless
                            </label>
                            <input id="order-document" type="file" name="document" class="form-control"
                                accept=".pdf,.doc,.docx,.xls,.xlsx" @unless($isEdit) required @endunless>
                            <div class="form-text">PDF, Word ან Excel · მაქსიმუმ 5 MB</div>
                            @if ($isEdit)
                                <div class="form-text">ფაილის არჩაურჩევლად არსებული დოკუმენტი დარჩება.</div>
                            @endif
                            @error('document') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="order-visibility" class="form-label">დოკუმენტის წვდომა <span class="text-danger">*</span></label>
                            <select id="order-visibility" name="document_visibility" class="form-select" required>
                                <option value="private" @selected(old('document_visibility', $order?->document_visibility ?? 'private') === 'private')>
                                    პირადი — მხოლოდ დაკავშირებული მომხმარებლებისთვის
                                </option>
                                <option value="public" @selected(old('document_visibility', $order?->document_visibility) === 'public')>
                                    საჯარო — შეიქმნას გასაზიარებელი ბმული
                                </option>
                            </select>
                            <div class="form-text">პირადზე გადართვისას ძველი საჯარო ბმული გაუქმდება.</div>
                            @error('document_visibility') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-4">
                        <h2 class="h6">ხელმომწერები <span class="text-danger">*</span></h2>
                        <p class="text-muted small">
                            აირჩიეთ ამ ფილიალთან დაკავშირებული პასუხისმგებელი პირები ან კომპანიის ხელმძღვანელები.
                        </p>
                        <div id="order-system-participants" class="row g-2"></div>
                        <div id="order-system-participants-empty" class="alert alert-warning d-none mb-0">
                            არჩეულ ფილიალთან დაკავშირებული ხელმომწერები ვერ მოიძებნა.
                        </div>
                        @error('user_participant_ids') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center gap-2 mt-4">
                        <a href="{{ $backRoute }}" class="btn btn-outline-secondary">უკან</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2-circle me-1"></i>
                            {{ $isEdit ? 'განახლება' : 'შექმნა' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const optionsByBranch = @json($participantOptions);
        const initiallySelected = new Set(@json($selectedUsers));
        const signedUserIds = new Set(@json(
            $isEdit
                ? $order->userParticipants->whereNotNull('signed_at')->pluck('user_id')->map(fn($id) => (int) $id)->values()
                : []
        ));
        const branchSelect = document.getElementById('order-branch');
        const participantContainer = document.getElementById('order-system-participants');
        const participantEmpty = document.getElementById('order-system-participants-empty');

        function renderParticipants() {
            const options = optionsByBranch[branchSelect.value] || [];
            participantContainer.innerHTML = '';
            participantEmpty.classList.toggle('d-none', options.length > 0);

            options.forEach(function (person) {
                const column = document.createElement('div');
                column.className = 'col-md-6';
                const wrapper = document.createElement('div');
                wrapper.className = 'form-check border rounded p-3 ps-5 h-100';
                const input = document.createElement('input');
                input.type = 'checkbox';
                input.className = 'form-check-input';
                input.name = 'user_participant_ids[]';
                input.value = person.id;
                input.id = 'order-user-' + person.id;
                input.checked = initiallySelected.has(Number(person.id));
                input.disabled = signedUserIds.has(Number(person.id));

                if (input.disabled) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'user_participant_ids[]';
                    hidden.value = person.id;
                    wrapper.appendChild(hidden);
                }

                const label = document.createElement('label');
                label.className = 'form-check-label';
                label.htmlFor = input.id;
                label.textContent = person.name + ' · ' + person.role_label
                    + (input.disabled ? ' · ხელმოწერილია' : '');

                wrapper.appendChild(input);
                wrapper.appendChild(label);
                column.appendChild(wrapper);
                participantContainer.appendChild(column);
            });
        }

        branchSelect.addEventListener('change', renderParticipants);
        renderParticipants();
    });
</script>
