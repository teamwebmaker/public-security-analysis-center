<?php

namespace App\Http\Requests;

use App\Models\Branch;
use App\Models\Employee;
use App\Services\Employees\EmployeeBranchAccess;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Employee::class) ?? false;
    }

    public function rules(): array
    {
        $branchAccess = app(EmployeeBranchAccess::class);
        $employee = $this->employeeForBranchAccess();

        return [
            'company_id' => [
                'required',
                Rule::in($branchAccess->companyIdsFor($this->user(), $employee)->all()),
            ],
            'branch_ids' => ['required', 'array', 'min:1'],
            'branch_ids.*' => [
                'integer',
                'distinct',
                Rule::in($branchAccess->branchIdsFor($this->user(), $employee)->all()),
            ],
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'id_number' => ['nullable', 'string', 'max:50'],
            'personal_details_visible' => $this->personalDetailsVisibilityRules(),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->filled('company_id') || ! is_array($this->input('branch_ids'))) {
                return;
            }

            $invalidBranchExists = Branch::query()
                ->whereIn('id', $this->input('branch_ids'))
                ->where('company_id', '!=', $this->integer('company_id'))
                ->exists();

            if ($invalidBranchExists) {
                $validator->errors()->add(
                    'branch_ids',
                    'ყველა არჩეული ფილიალი არჩეულ კომპანიას უნდა ეკუთვნოდეს.'
                );
            }
        });
    }

    protected function employeeForBranchAccess(): ?Employee
    {
        return null;
    }

    protected function personalDetailsVisibilityRules(): array
    {
        return ['required', 'boolean'];
    }
}
