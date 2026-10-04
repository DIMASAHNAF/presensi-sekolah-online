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
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'email' => 'nullable|email|unique:users,email',
            'nisn' => 'required|string|digits:10|unique:users,nisn',
            'kelas_id' => 'nullable|exists:kelas,id',
            'password' => 'required|string|min:6',
        ];
    }
}
