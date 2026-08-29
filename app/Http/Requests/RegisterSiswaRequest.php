<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Nama lengkap wajib diisi.',
            'username.required'   => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, (-), dan (_).',
            'username.unique'     => 'Username sudah digunakan.',
            'email.required'      => 'Email wajib diisi.',
            'email.email'         => 'Format email tidak valid.',
            'email.unique'        => 'Email sudah terdaftar.',
            'nisn.required'       => 'NISN wajib diisi.',
            'nisn.digits'         => 'NISN harus 10 digit angka.',
            'nisn.unique'         => 'NISN sudah terdaftar.',
            'password.required'   => 'Password wajib diisi.',
            'password.min'        => 'Password minimal 6 karakter.',
            'password.confirmed'  => 'Konfirmasi password tidak cocok.',
        ];
    }
}