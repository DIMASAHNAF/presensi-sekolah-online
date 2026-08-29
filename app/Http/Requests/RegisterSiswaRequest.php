<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'nisn'     => ['required', 'string', 'digits:10', 'unique:users,nisn'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, strip (-), dan underscore (_).',
            'username.unique'     => 'Username sudah digunakan.',
            'email.unique'        => 'Email sudah terdaftar.',
            'nisn.digits'         => 'NISN harus terdiri dari 10 digit angka.',
            'nisn.unique'         => 'NISN sudah terdaftar.',
            'password.confirmed'  => 'Konfirmasi password tidak cocok.',
        ];
    }
}