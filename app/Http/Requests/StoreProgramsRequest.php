<?php

namespace App\Http\Requests;

use DateTimeImmutable;
use Illuminate\Foundation\Http\FormRequest;

class StoreProgramsRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'start_date' => $this->normalizeDate($this->input('start_date')),
            'end_date' => $this->normalizeDate($this->input('end_date')),
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title_ka' => 'required|string|min:3|max:200',
            'title_en' => 'required|string|min:3|max:200',
            'description_ka' => 'required|string|min:10',
            'description_en' => 'required|string|min:10',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'certificate_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'video' => 'nullable|url',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|string|max:100',
            'address' => 'required|string|max:225',
            'start_date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'hour_start' => 'required|date_format:H:i',
            'hour_end' => 'required|date_format:H:i|after:hour_start',
            'days' => 'required|array|min:1',
            'days.*' => 'string',
            'mentor_ids' => 'nullable|array',
            'mentor_ids.*' => 'exists:mentors,id',
            'visibility' => 'required|in:1,0',
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.date_format' => 'საწყისი თარიღი უნდა იყოს ფორმატში dd/mm/yyyy.',
            'end_date.date_format' => 'დასასრული თარიღი უნდა იყოს ფორმატში dd/mm/yyyy.',
        ];
    }

    private function normalizeDate(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $value = trim($value);
        if (! preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
            return $value;
        }

        $date = DateTimeImmutable::createFromFormat('!d/m/Y', $value);
        $errors = DateTimeImmutable::getLastErrors();

        if (
            $date === false
            || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
            || $date->format('d/m/Y') !== $value
        ) {
            return $value;
        }

        return $date->format('Y-m-d');
    }
}
