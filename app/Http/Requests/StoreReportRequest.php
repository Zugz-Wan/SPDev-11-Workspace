<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'facility_id' => ['required', 'integer', 'exists:facilities,id'],
            'kategori' => ['required', 'string', 'max:100'],
            'deskripsi' => ['required', 'string', 'min:5', 'max:2000'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'facility_id.required' => 'Silakan pilih fasilitas yang dilaporkan.',
            'facility_id.exists' => 'Fasilitas yang dipilih tidak ditemukan.',
            'kategori.required' => 'Kategori kerusakan/masalah wajib dipilih.',
            'deskripsi.required' => 'Deskripsi kerusakan wajib diisi.',
            'deskripsi.min' => 'Deskripsi kerusakan minimal :min karakter.',
            'foto.image' => 'File bukti harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus berupa JPEG, PNG, JPG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal adalah 5MB.',
        ];
    }
}
