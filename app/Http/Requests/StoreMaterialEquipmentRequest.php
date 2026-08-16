<?php

namespace App\Http\Requests;

use App\Models\MaterialEquipment;
use App\Models\User;
use App\Services\MaterialEquipment\MaterialEquipmentBranchAccess;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMaterialEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', MaterialEquipment::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'branch_id' => [
                'required',
                Rule::in(app(MaterialEquipmentBranchAccess::class)
                    ->idsForUser($this->user(), $this->materialEquipment())
                    ->all()),
            ],
            'document' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:5120'],
            'document_visibility' => ['required', Rule::in([
                MaterialEquipment::VISIBILITY_PRIVATE,
                MaterialEquipment::VISIBILITY_PUBLIC,
            ])],
            'signer_user_ids' => ['required', 'array', 'min:1'],
            'signer_user_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->filled('branch_id') || ! is_array($this->input('signer_user_ids'))) {
                return;
            }

            $eligibleIds = $this->eligibleSignerIds((int) $this->input('branch_id'))
                ->merge($this->additionalAllowedSignerIds())
                ->unique();
            $invalidIds = collect($this->input('signer_user_ids'))
                ->map(fn ($id) => (int) $id)
                ->diff($eligibleIds);

            if ($invalidIds->isNotEmpty()) {
                $validator->errors()->add(
                    'signer_user_ids',
                    'არჩეული პირებიდან ერთი ან მეტი ამ ფილიალთან დაკავშირებული არ არის.'
                );
            }
        });
    }

    protected function materialEquipment(): ?MaterialEquipment
    {
        return null;
    }

    protected function additionalAllowedSignerIds()
    {
        return collect();
    }

    private function eligibleSignerIds(int $branchId)
    {
        return User::query()
            ->where('is_active', true)
            ->where(function ($query) use ($branchId) {
                $query->where(function ($query) use ($branchId) {
                    $query->whereHas('role', fn ($role) => $role->where('name', 'responsible_person'))
                        ->whereHas('branches', fn ($branch) => $branch->where('branches.id', $branchId));
                })->orWhere(function ($query) use ($branchId) {
                    $query->whereHas('role', fn ($role) => $role->where('name', 'company_leader'))
                        ->whereHas('companies.branches', fn ($branch) => $branch->where('branches.id', $branchId));
                });
            })
            ->pluck('id');
    }
}
