<?php

namespace App\Http\Requests;

use App\Models\Incident;
use Illuminate\Validation\Rule;

class UpdateIncidentRequest extends StoreIncidentRequest
{
    public function authorize(): bool
    {
        $incident = $this->route('incident');

        return $incident instanceof Incident
            && ($this->user()?->can('update', $incident) ?? false);
    }

    public function rules(): array
    {
        $incident = $this->route('incident');
        $rules = parent::rules();
        $rules['branch_id'] = [
            'required',
            Rule::in([(string) $incident->branch_id, (int) $incident->branch_id]),
        ];
        $rules['document'] = ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:5120'];
        $rules['external_participants.*.id'] = ['nullable', 'integer', 'exists:incident_external_participants,id'];

        return $rules;
    }

    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        parent::withValidator($validator);

        $validator->after(function (\Illuminate\Validation\Validator $validator): void {
            $incident = $this->route('incident');

            if (
                $this->hasFile('document')
                && (
                    $incident->userParticipants()->whereNotNull('signed_at')->exists()
                    || $incident->externalParticipants()->whereNotNull('signed_at')->exists()
                )
            ) {
                $validator->errors()->add(
                    'document',
                    'ხელმოწერილი ინციდენტის დოკუმენტის ჩანაცვლება შეუძლებელია.'
                );
            }

            foreach ((array) $this->input('external_participants', []) as $participant) {
                $id = (int) ($participant['id'] ?? 0);
                if ($id > 0 && !$incident->externalParticipants()->whereKey($id)->exists()) {
                    $validator->errors()->add(
                        'external_participants',
                        'მითითებული გარე პირი ამ ინციდენტს არ ეკუთვნის.'
                    );
                }
            }
        });
    }

    protected function additionalAllowedParticipantIds()
    {
        return $this->route('incident')
            ->userParticipants()
            ->whereNotNull('signed_at')
            ->pluck('user_id');
    }
}
