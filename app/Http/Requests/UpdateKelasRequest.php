<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $kelas = $this->route('kelas');
        return [
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas,'.$kelas->id,
            'tingkat' => 'required|in:X,XI,XII',
            'jurusan' => 'nullable|string|max:50',
        ];
    }
}
