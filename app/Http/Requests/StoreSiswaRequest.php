<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaRequest extends FormRequest
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
            'nisn' => 'required|string|digits:10|unique:users,nisn',
            'kelas_id' => 'nullable|exists:kelas,id',
            'password' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap siswa wajib diisi.',
            'username.required' => 'Username akun wajib diisi.',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik (.), strip (-), dan garis bawah (_).',
            'username.unique' => 'Username ini sudah terdaftar. Silakan gunakan username lain.',
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.digits' => 'NISN harus terdiri dari tepat 10 digit angka.',
            'nisn.unique' => 'NISN ini sudah terdaftar untuk siswa lain.',
            'kelas_id.exists' => 'Kelas yang dipilih tidak valid.',
            'password.required' => 'Password awal wajib diisi.',
            'password.min' => 'Password minimal harus 6 karakter.',
        ];
    }
}
