<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateOrderRequest extends StoreOrderRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');

        return $order instanceof Order
            && ($this->user()?->can('update', $order) ?? false);
    }

    public function rules(): array
    {
        $order = $this->route('order');
        $rules = parent::rules();
        $rules['branch_id'] = [
            'required',
            Rule::in([(string) $order->branch_id, (int) $order->branch_id]),
        ];
        $rules['document'] = ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:5120'];

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        parent::withValidator($validator);

        $validator->after(function (Validator $validator): void {
            $order = $this->route('order');

            if ($this->hasFile('document') && $order->userParticipants()->whereNotNull('signed_at')->exists()) {
                $validator->errors()->add(
                    'document',
                    'ხელმოწერილი ბრძანების დოკუმენტის ჩანაცვლება შეუძლებელია.'
                );
            }
        });
    }

    protected function additionalAllowedParticipantIds()
    {
        return $this->route('order')
            ->userParticipants()
            ->whereNotNull('signed_at')
            ->pluck('user_id');
    }
}
