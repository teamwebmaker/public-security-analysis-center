<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMainMenuRequest extends FormRequest
{
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
            "title_ka" => "required|string|min:3|max:200",
            "title_en" => "required|string|min:3|max:200",
            "visibility" => "required|in:1,0",
            'sorted' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('main_menus', 'sorted')->ignore($this->main_menu->id), // assuming route-model binding
            ],
        ];

    }
    public function messages()
    {
        return [
            'sorted.unique' => 'რიგითობა მენიუში უნდა იყოს უნიკალური.',
        ];
    }

}
