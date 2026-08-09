<?php

namespace App\Http\Requests;

use App\Models\Employee;

class UpdateEmployeeRequest extends StoreEmployeeRequest
{
    public function authorize(): bool
    {
        $employee = $this->route('employee');

        return $employee instanceof Employee
            && ($this->user()?->can('update', $employee) ?? false);
    }

    protected function employeeForBranchAccess(): ?Employee
    {
        $employee = $this->route('employee');

        return $employee instanceof Employee ? $employee : null;
    }

    protected function personalDetailsVisibilityRules(): array
    {
        $employee = $this->employeeForBranchAccess();

        if (! $employee || ! ($this->user()?->can('changePersonalDetailsVisibility', $employee) ?? false)) {
            return ['prohibited'];
        }

        return ['required', 'boolean'];
    }
}
