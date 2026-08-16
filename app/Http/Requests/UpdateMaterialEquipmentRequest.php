<?php

namespace App\Http\Requests;

use App\Models\MaterialEquipment;
use Illuminate\Validation\Rule;

class UpdateMaterialEquipmentRequest extends StoreMaterialEquipmentRequest
{
    public function authorize(): bool
    {
        $materialEquipment = $this->materialEquipment();

        return $materialEquipment instanceof MaterialEquipment
            && ($this->user()?->can('update', $materialEquipment) ?? false);
    }

    public function rules(): array
    {
        $materialEquipment = $this->materialEquipment();
        $rules = parent::rules();
        $rules['branch_id'] = [
            'required',
            Rule::in([(string) $materialEquipment->branch_id, (int) $materialEquipment->branch_id]),
        ];
        $rules['document'] = ['prohibited'];

        return $rules;
    }

    protected function materialEquipment(): ?MaterialEquipment
    {
        $materialEquipment = $this->route('materialEquipment');

        return $materialEquipment instanceof MaterialEquipment ? $materialEquipment : null;
    }

    protected function additionalAllowedSignerIds()
    {
        return $this->materialEquipment()->signatures()->pluck('user_id');
    }
}
