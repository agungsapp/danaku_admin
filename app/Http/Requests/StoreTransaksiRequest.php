<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransaksiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization will be handled in controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dompet_id' => [
                'required',
                'integer',
                'exists:dompets,id',
            ],
            'kategori_id' => [
                'required',
                'integer',
                'exists:kategoris,id',
            ],
            'judul' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'dompet_id.required' => 'Dompet harus dipilih',
            'dompet_id.exists' => 'Dompet tidak ditemukan',
            'kategori_id.required' => 'Kategori harus dipilih',
            'kategori_id.exists' => 'Kategori tidak ditemukan',
            'judul.required' => 'Judul transaksi harus diisi',
            'jumlah.required' => 'Jumlah transaksi harus diisi',
            'jumlah.min' => 'Jumlah transaksi minimal 1',
        ];
    }
}
