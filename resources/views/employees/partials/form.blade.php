@php
    $isEdit = isset($employee) && $employee !== null;
    $selectedBranchIds = collect(old(
        'branch_ids',
        $isEdit ? $employee->branches->pluck('id')->all() : []
    ))->map(fn($id) => (int) $id);
    $selectedCompanyId = old('company_id', $employee?->company_id);
    $canChangeVisibility = !$isEdit || $canChangePersonalDetailsVisibility;
@endphp

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-4">{{ $formTitle }}</h1>

                @if ($companies->isEmpty())
                    <div class="alert alert-warning">
                        თქვენთან დაკავშირებული ხელმისაწვდომი კომპანია და ფილიალი ვერ მოიძებნა.
                    </div>
                @endif

                <form method="POST" action="{{ $formAction }}">
                    @csrf
                    @if ($formMethod !== 'POST')
                        @method($formMethod)
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="employee-company" class="form-label">კომპანია <span class="text-danger">*</span></label>
                            <select id="employee-company" name="company_id" class="form-select" required>
                                <option value="">აირჩიეთ კომპანია</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}"
                                        @selected((string) $selectedCompanyId === (string) $company->id)>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="employee-position" class="form-label">პოზიცია <span class="text-danger">*</span></label>
                            <input id="employee-position" type="text" name="position" class="form-control"
                                value="{{ old('position', $employee?->position) }}" maxlength="255" required>
                            @error('position') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="employee-name" class="form-label">სახელი <span class="text-danger">*</span></label>
                            <input id="employee-name" type="text" name="name" class="form-control"
                                value="{{ old('name', $employee?->name) }}" maxlength="255" required>
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="employee-surname" class="form-label">გვარი <span class="text-danger">*</span></label>
                            <input id="employee-surname" type="text" name="surname" class="form-control"
                                value="{{ old('surname', $employee?->surname) }}" maxlength="255" required>
                            @error('surname') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label">ფილიალები <span class="text-danger">*</span></label>
                        <p class="text-muted small mb-2">შესაძლებელია არჩეული კომპანიის რამდენიმე ფილიალის მონიშვნა.</p>
                        <div id="employee-branches" class="row g-2">
                            @foreach ($branches as $branch)
                                <div class="col-md-6 employee-branch-option" data-company-id="{{ $branch->company_id }}">
                                    <div class="form-check border rounded p-3 ps-5 h-100">
                                        <input id="employee-branch-{{ $branch->id }}" type="checkbox"
                                            class="form-check-input" name="branch_ids[]" value="{{ $branch->id }}"
                                            @checked($selectedBranchIds->contains((int) $branch->id))>
                                        <label for="employee-branch-{{ $branch->id }}" class="form-check-label">
                                            {{ $branch->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div id="employee-branches-empty" class="alert alert-light border mt-2 d-none">
                            არჩეულ კომპანიაში ხელმისაწვდომი ფილიალი ვერ მოიძებნა.
                        </div>
                        @error('branch_ids') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        @error('branch_ids.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <hr class="my-4">
                    <h2 class="h6 mb-3">პირადი მონაცემები</h2>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="employee-phone" class="form-label">ტელეფონი</label>
                            <input id="employee-phone" type="tel" name="phone" class="form-control"
                                value="{{ old('phone', $employee?->phone) }}" maxlength="50">
                            @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="employee-email" class="form-label">ელფოსტა</label>
                            <input id="employee-email" type="email" name="email" class="form-control"
                                value="{{ old('email', $employee?->email) }}" maxlength="255">
                            @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="employee-id-number" class="form-label">პირადი ნომერი</label>
                            <input id="employee-id-number" type="text" name="id_number" class="form-control"
                                value="{{ old('id_number', $employee?->id_number) }}" maxlength="50">
                            @error('id_number') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            @if ($canChangeVisibility)
                                <label for="employee-personal-visibility" class="form-label">
                                    პირადი მონაცემების ხილვადობა <span class="text-danger">*</span>
                                </label>
                                <select id="employee-personal-visibility" name="personal_details_visible"
                                    class="form-select" required>
                                    <option value="0" @selected((string) old('personal_details_visible', (int) ($employee?->personal_details_visible ?? false)) === '0')>
                                        დამალული — მხოლოდ შემქმნელი და ადმინისტრატორი
                                    </option>
                                    <option value="1" @selected((string) old('personal_details_visible', (int) ($employee?->personal_details_visible ?? false)) === '1')>
                                        ხილული — ყველა უფლებამოსილი მნახველი
                                    </option>
                                </select>
                                @error('personal_details_visible') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            @else
                                <div class="form-label">პირადი მონაცემების ხილვადობა</div>
                                <div>
                                    <span class="badge {{ $employee->personal_details_visible ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $employee->personal_details_visible ? 'ხილული' : 'დამალული' }}
                                    </span>
                                </div>
                                <div class="form-text">ხილვადობის შეცვლა მხოლოდ ჩანაწერის შემქმნელს შეუძლია.</div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center gap-2 mt-4">
                        <a href="{{ $backRoute }}" class="btn btn-outline-secondary">უკან</a>
                        <button type="submit" class="btn btn-primary" @disabled($companies->isEmpty())>
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
        const companySelect = document.getElementById('employee-company');
        const branchOptions = Array.from(document.querySelectorAll('.employee-branch-option'));
        const emptyMessage = document.getElementById('employee-branches-empty');
        let previousCompanyId = companySelect.value;

        function renderBranches() {
            const companyId = companySelect.value;
            let visibleCount = 0;

            branchOptions.forEach(function (option) {
                const visible = companyId !== '' && option.dataset.companyId === companyId;
                option.classList.toggle('d-none', !visible);

                const checkbox = option.querySelector('input[type="checkbox"]');
                checkbox.disabled = !visible;

                if (!visible && previousCompanyId !== companyId) {
                    checkbox.checked = false;
                }

                if (visible) {
                    visibleCount += 1;
                }
            });

            emptyMessage.classList.toggle('d-none', companyId === '' || visibleCount > 0);
            previousCompanyId = companyId;
        }

        companySelect.addEventListener('change', renderBranches);
        renderBranches();
    });
</script>
