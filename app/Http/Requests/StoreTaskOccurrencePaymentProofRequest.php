<?php

namespace App\Http\Requests;

use App\Models\TaskOccurrence;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskOccurrencePaymentProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        $taskOccurrence = $this->route('taskOccurrence');

        return $taskOccurrence instanceof TaskOccurrence
            && ($this->user()?->can('uploadPaymentProof', $taskOccurrence) ?? false);
    }

    public function rules(): array
    {
        return [
            'payment_proof' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_proof.required' => 'გთხოვთ, ატვირთოთ გადახდის დამადასტურებელი დოკუმენტი.',
            'payment_proof.mimes' => 'დაშვებულია მხოლოდ PDF, JPG, JPEG, PNG ან WEBP ფაილი.',
            'payment_proof.max' => 'ფაილის მაქსიმალური ზომაა 5MB.',
        ];
    }
}
