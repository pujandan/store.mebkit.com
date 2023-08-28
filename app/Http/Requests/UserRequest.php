<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Rules\Password;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // always allow true because use in register
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'username' => 'required|string|max:255',
            'email' => [
                'required', 'string', 'max:255', 'email',
                Rule::unique('users', 'email')->ignore($this->id)
            ],
            'password' => [
                'nullable', 'string',
                Rule::requiredIf($this->id == null),
                new Password
            ],
        ];
    }

    public function prepareForValidation()
    {
        // $this->merge([
        //     'field_new' => $this->in_request,
        // ]);
    }
}
