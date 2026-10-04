<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'Username, email, atau NISN wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ];
    }
}
