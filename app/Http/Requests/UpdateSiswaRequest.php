<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $siswa = $this->route('siswa');
        return [
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9._-]+$/', 'unique:users,username,'.$siswa->id],
            'email' => 'nullable|email|unique:users,email,'.$siswa->id,
            'nisn' => 'required|string|digits:10|unique:users,nisn,'.$siswa->id,
            'kelas_id' => 'nullable|exists:kelas,id',
            'password' => 'nullable|string|min:6',
            'no_hp_ortu' => 'nullable|string|max:30',
            'nama_ortu' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'remove_avatar' => 'nullable|boolean',
            'remove_banner' => 'nullable|boolean',
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
            'password.min' => 'Password minimal harus 6 karakter.',
        ];
    }
}
