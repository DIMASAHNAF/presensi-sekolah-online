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

    public function messages(): array
    {
        return [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique' => 'Nama kelas ini sudah terdaftar.',
            'tingkat.required' => 'Tingkat kelas wajib dipilih.',
            'tingkat.in' => 'Tingkat kelas harus X, XI, atau XII.',
        ];
    }
}
