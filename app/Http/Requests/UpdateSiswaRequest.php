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
            'username' => 'required|string|max:50|alpha_dash|unique:users,username,'.$siswa->id,
            'email' => 'nullable|email|unique:users,email,'.$siswa->id,
            'nisn' => 'required|string|digits:10|unique:users,nisn,'.$siswa->id,
            'kelas_id' => 'nullable|exists:kelas,id',
        ];
    }
}
