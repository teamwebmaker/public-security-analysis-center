<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\User;
use App\Services\Orders\OrderBranchAccess;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Order::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'branch_id' => [
                'required',
                Rule::in(app(OrderBranchAccess::class)->idsForUser($this->user())->all()),
            ],
            'document' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:5120'],
            'document_visibility' => ['required', Rule::in([
                Order::VISIBILITY_PRIVATE,
                Order::VISIBILITY_PUBLIC,
            ])],
            'user_participant_ids' => ['required', 'array', 'min:1'],
            'user_participant_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (!$this->filled('branch_id') || !is_array($this->input('user_participant_ids'))) {
                return;
            }

            $eligibleIds = $this->eligibleParticipantIds((int) $this->input('branch_id'))
                ->merge($this->additionalAllowedParticipantIds())
                ->unique();
            $invalidIds = collect($this->input('user_participant_ids'))
                ->map(fn($id) => (int) $id)
                ->diff($eligibleIds);

            if ($invalidIds->isNotEmpty()) {
                $validator->errors()->add(
                    'user_participant_ids',
                    'არჩეული პირებიდან ერთი ან მეტი ამ ფილიალთან დაკავშირებული არ არის.'
                );
            }
        });
    }

    protected function eligibleParticipantIds(int $branchId)
    {
        return User::query()
            ->where('is_active', true)
            ->where(function ($query) use ($branchId) {
                $query->where(function ($query) use ($branchId) {
                    $query->whereHas('role', fn($role) => $role->where('name', 'responsible_person'))
                        ->whereHas('branches', fn($branch) => $branch->where('branches.id', $branchId));
                })->orWhere(function ($query) use ($branchId) {
                    $query->whereHas('role', fn($role) => $role->where('name', 'company_leader'))
                        ->whereHas('companies.branches', fn($branch) => $branch->where('branches.id', $branchId));
                });
            })
            ->pluck('id');
    }

    protected function additionalAllowedParticipantIds()
    {
        return collect();
    }
}
