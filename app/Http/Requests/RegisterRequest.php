<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|min:3|max:255',
            'father_name' => 'required|string|min:3|max:255',
            'cnic' => 'required|string|size:15|unique:users,cnic',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|size:12',
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'cnic.size' => 'CNIC must be formatted as 00000-0000000-0.',
            'phone.size' => 'Phone number must be formatted as 0300-0000000.',
        ];
    }
}
