@php
    $isEdit = isset($incident) && $incident !== null;
    $selectedUsers = collect(old(
        'user_participant_ids',
        $isEdit ? $incident->userParticipants->pluck('user_id')->all() : []
    ))->map(fn($id) => (int) $id)->values();
    $externalPeople = collect(old(
        'external_participants',
        $isEdit
            ? $incident->externalParticipants->map(fn($person) => [
                'id' => $person->id,
                'full_name' => $person->full_name,
                'phone' => $person->phone,
                'signed' => $person->signed_at !== null,
            ])->all()
            : []
    ))->values();
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
                            <label for="incident-title" class="form-label">დასახელება <span class="text-danger">*</span></label>
                            <input id="incident-title" type="text" name="title" class="form-control"
                                value="{{ old('title', $incident?->title) }}" maxlength="255" required>
                            @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="incident-branch" class="form-label">ფილიალი <span class="text-danger">*</span></label>
                            <select id="incident-branch" name="branch_id" class="form-select" required
                                @if($isEdit) disabled @endif>
                                <option value="">აირჩიეთ ფილიალი</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        @selected((string) old('branch_id', $incident?->branch_id) === (string) $branch->id)>
                                        {{ $branch->name }} · {{ $branch->company?->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($isEdit)
                                <input type="hidden" name="branch_id" value="{{ $incident->branch_id }}">
                            @endif
                            @error('branch_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="incident-document" class="form-label">
                                დოკუმენტი @unless($isEdit)<span class="text-danger">*</span>@endunless
                            </label>
                            <input id="incident-document" type="file" name="document" class="form-control"
                                accept=".pdf,.doc,.docx,.xls,.xlsx" @unless($isEdit) required @endunless>
                            <div class="form-text">PDF, Word ან Excel · მაქსიმუმ 5 MB</div>
                            @if ($isEdit)
                                <div class="form-text">ფაილის არჩაურჩევლად არსებული დოკუმენტი დარჩება.</div>
                            @endif
                            @error('document') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="incident-visibility" class="form-label">დოკუმენტის წვდომა <span class="text-danger">*</span></label>
                            <select id="incident-visibility" name="document_visibility" class="form-select" required>
                                <option value="private" @selected(old('document_visibility', $incident?->document_visibility ?? 'private') === 'private')>
                                    პირადი — მხოლოდ დაკავშირებული მომხმარებლებისთვის
                                </option>
                                <option value="public" @selected(old('document_visibility', $incident?->document_visibility) === 'public')>
                                    საჯარო — შეიქმნას გასაზიარებელი ბმული
                                </option>
                            </select>
                            <div class="form-text">პირადზე გადართვისას ძველი საჯარო ბმული გაუქმდება.</div>
                            @error('document_visibility') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-4">
                        <h2 class="h6">სისტემის მომხმარებლების ხელმოწერები <span class="text-danger">*</span></h2>
                        <p class="text-muted small">აირჩიეთ ამ ფილიალთან დაკავშირებული პასუხისმგებელი პირები ან კომპანიის ხელმძღვანელები.</p>
                        <div id="incident-system-participants" class="row g-2"></div>
                        <div id="incident-system-participants-empty" class="alert alert-warning d-none mb-0">
                            არჩეულ ფილიალთან დაკავშირებული ხელმომწერები ვერ მოიძებნა.
                        </div>
                        @error('user_participant_ids') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                            <div>
                                <h2 class="h6 mb-1">სხვა მონაწილე პირები</h2>
                                <p class="text-muted small mb-0">სახელი სავალდებულოა, ტელეფონი — არა.</p>
                            </div>
                            <button type="button" id="add-external-participant" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-person-plus me-1"></i>
                                პირის დამატება
                            </button>
                        </div>
                        <div id="external-participants-list" class="d-grid gap-2"></div>
                        @error('external_participants') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
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
                ? $incident->userParticipants->whereNotNull('signed_at')->pluck('user_id')->map(fn($id) => (int) $id)->values()
                : []
        ));
        const branchSelect = document.getElementById('incident-branch');
        const participantContainer = document.getElementById('incident-system-participants');
        const participantEmpty = document.getElementById('incident-system-participants-empty');

        function renderSystemParticipants() {
            const branchId = branchSelect.value;
            const options = optionsByBranch[branchId] || [];
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
                input.id = 'incident-user-' + person.id;
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
                label.textContent = person.name + ' · ' + person.role_label + (input.disabled ? ' · ხელმოწერილია' : '');

                wrapper.appendChild(input);
                wrapper.appendChild(label);
                column.appendChild(wrapper);
                participantContainer.appendChild(column);
            });
        }

        branchSelect.addEventListener('change', renderSystemParticipants);
        renderSystemParticipants();

        const externalList = document.getElementById('external-participants-list');
        const initialExternalPeople = @json($externalPeople);
        let externalIndex = 0;

        function addExternalParticipant(person = {}) {
            const index = externalIndex++;
            const row = document.createElement('div');
            row.className = 'row g-2 align-items-end border rounded p-2 mx-0';

            const locked = Boolean(person.signed);
            row.innerHTML = `
                <input type="hidden" name="external_participants[${index}][id]" value="${person.id || ''}">
                <div class="col-md-5">
                    <label class="form-label small">სახელი და გვარი</label>
                    <input type="text" class="form-control" name="external_participants[${index}][full_name]"
                        maxlength="255" value="" ${locked ? 'readonly' : ''}>
                </div>
                <div class="col-md-5">
                    <label class="form-label small">ტელეფონი</label>
                    <input type="text" class="form-control" name="external_participants[${index}][phone]"
                        maxlength="30" value="" ${locked ? 'readonly' : ''}>
                </div>
                <div class="col-md-2 d-grid">
                    ${locked
                        ? '<span class="badge text-bg-success py-2">ხელმოწერილია</span>'
                        : '<button type="button" class="btn btn-outline-danger remove-external-person"><i class="bi bi-trash"></i></button>'}
                </div>
            `;
            row.querySelector(`[name="external_participants[${index}][full_name]"]`).value = person.full_name || '';
            row.querySelector(`[name="external_participants[${index}][phone]"]`).value = person.phone || '';
            row.querySelector('.remove-external-person')?.addEventListener('click', () => row.remove());
            externalList.appendChild(row);
        }

        document.getElementById('add-external-participant')
            .addEventListener('click', () => addExternalParticipant());
        initialExternalPeople.forEach(addExternalParticipant);
    });
</script>
