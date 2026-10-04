<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $guru = $this->route('guru');
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,'.$guru->id,
            'email' => 'nullable|email|unique:users,email,'.$guru->id,
            'nik' => 'required|string|max:20|unique:users,nik,'.$guru->id,
        ];
    }
}
