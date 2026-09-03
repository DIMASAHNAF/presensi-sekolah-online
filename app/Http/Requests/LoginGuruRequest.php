<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nik' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nik.required' => 'NIK wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ];
    }
}
