<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9._-]+$/', 'unique:users,username'],
            'email' => 'nullable|email|unique:users,email',
            'nik' => 'required|string|max:20|unique:users,nik',
            'password' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap guru wajib diisi.',
            'username.required' => 'Username login wajib diisi.',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik (.), strip (-), dan garis bawah (_).',
            'username.unique' => 'Username ini sudah terdaftar. Silakan pilih username lain.',
            'nik.required' => 'NIK guru wajib diisi.',
            'nik.unique' => 'NIK ini sudah terdaftar untuk guru lain.',
            'password.required' => 'Password awal wajib diisi.',
            'password.min' => 'Password minimal harus 6 karakter.',
        ];
    }
}
