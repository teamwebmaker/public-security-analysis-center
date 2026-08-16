@php
    $isEdit = isset($materialEquipment) && $materialEquipment !== null;
    $selectedUsers = collect(old(
        'signer_user_ids',
        $isEdit ? $materialEquipment->signatures->pluck('user_id')->all() : []
    ))->map(fn ($id) => (int) $id)->values();
@endphp

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-4">{{ $formTitle }}</h1>

                @if ($branches->isEmpty())
                    <div class="alert alert-warning">
                        თქვენთან დაკავშირებული ხელმისაწვდომი ფილიალი ვერ მოიძებნა.
                    </div>
                @endif

                <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data">
                    @csrf
                    @if ($formMethod !== 'POST')
                        @method($formMethod)
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="material-equipment-title" class="form-label">
                                დასახელება <span class="text-danger">*</span>
                            </label>
                            <input id="material-equipment-title" type="text" name="title" class="form-control"
                                value="{{ old('title', $materialEquipment?->title) }}" maxlength="255" required>
                            @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="material-equipment-branch" class="form-label">
                                ფილიალი <span class="text-danger">*</span>
                            </label>
                            <select id="material-equipment-branch" name="branch_id" class="form-select" required
                                @disabled($isEdit)>
                                <option value="">აირჩიეთ ფილიალი</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        @selected((string) old('branch_id', $materialEquipment?->branch_id) === (string) $branch->id)>
                                        {{ $branch->name }} · {{ $branch->company?->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($isEdit)
                                <input type="hidden" name="branch_id" value="{{ $materialEquipment->branch_id }}">
                                <div class="form-text">ხელმოწერების მთლიანობისთვის ფილიალის შეცვლა შეუძლებელია.</div>
                            @endif
                            @error('branch_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            @if ($isEdit)
                                <div class="form-label">დოკუმენტი</div>
                                <div class="form-control bg-body-secondary">{{ $materialEquipment->document_original_name }}</div>
                                <div class="form-text">უკვე ხელმოწერილი დოკუმენტის ჩანაცვლება შეუძლებელია.</div>
                            @else
                                <label for="material-equipment-document" class="form-label">
                                    დოკუმენტი <span class="text-danger">*</span>
                                </label>
                                <input id="material-equipment-document" type="file" name="document" class="form-control"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx" required>
                                <div class="form-text">PDF, Word ან Excel · მაქსიმუმ 5 MB</div>
                                @error('document') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label for="material-equipment-visibility" class="form-label">
                                დოკუმენტის წვდომა <span class="text-danger">*</span>
                            </label>
                            <select id="material-equipment-visibility" name="document_visibility" class="form-select" required>
                                <option value="private"
                                    @selected(old('document_visibility', $materialEquipment?->document_visibility ?? 'private') === 'private')>
                                    პირადი — მხოლოდ უფლებამოსილი მომხმარებლებისთვის
                                </option>
                                <option value="public"
                                    @selected(old('document_visibility', $materialEquipment?->document_visibility) === 'public')>
                                    საჯარო — შეიქმნას გასაზიარებელი ბმული
                                </option>
                            </select>
                            <div class="form-text">პირადზე გადართვისას ძველი საჯარო ბმული გაუქმდება.</div>
                            @error('document_visibility') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-4">
                        <h2 class="h6">უკვე ხელმომწერი პირები <span class="text-danger">*</span></h2>
                        <p class="text-muted small">
                            მონიშნეთ ფილიალთან დაკავშირებული კომპანიის ხელმძღვანელები და პასუხისმგებელი პირები,
                            რომლებმაც დოკუმენტს უკვე მოაწერეს ხელი.
                        </p>
                        <div id="material-equipment-signers" class="row g-2"></div>
                        <div id="material-equipment-signers-empty" class="alert alert-warning d-none mb-0">
                            არჩეულ ფილიალთან დაკავშირებული პირები ვერ მოიძებნა.
                        </div>
                        @error('signer_user_ids') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                        @error('signer_user_ids.*') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center gap-2 mt-4">
                        <a href="{{ $backRoute }}" class="btn btn-outline-secondary">უკან</a>
                        <button type="submit" class="btn btn-primary" @disabled($branches->isEmpty())>
                            <i class="bi bi-check2-circle me-1"></i>{{ $isEdit ? 'განახლება' : 'შექმნა' }}
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
        const branchSelect = document.getElementById('material-equipment-branch');
        const signerContainer = document.getElementById('material-equipment-signers');
        const signerEmpty = document.getElementById('material-equipment-signers-empty');

        function renderSigners() {
            const options = optionsByBranch[branchSelect.value] || [];
            signerContainer.innerHTML = '';
            signerEmpty.classList.toggle('d-none', options.length > 0);

            options.forEach(function (person) {
                const column = document.createElement('div');
                column.className = 'col-md-6';
                const wrapper = document.createElement('div');
                wrapper.className = 'form-check border rounded p-3 ps-5 h-100';
                const input = document.createElement('input');
                input.type = 'checkbox';
                input.className = 'form-check-input';
                input.name = 'signer_user_ids[]';
                input.value = person.id;
                input.id = 'material-equipment-signer-' + person.id;
                input.checked = initiallySelected.has(Number(person.id));

                const label = document.createElement('label');
                label.className = 'form-check-label';
                label.htmlFor = input.id;
                label.textContent = person.name + ' · ' + person.role_label;

                wrapper.appendChild(input);
                wrapper.appendChild(label);
                column.appendChild(wrapper);
                signerContainer.appendChild(column);
            });
        }

        branchSelect.addEventListener('change', renderSigners);
        renderSigners();
    });
</script>
