<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Public access via token, no auth required
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'decision' => 'required|in:approved,rejected',
            'notes' => 'required_if:decision,rejected|nullable|string|max:1000'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'decision.required' => 'Keputusan verifikasi harus dipilih',
            'decision.in' => 'Keputusan tidak valid. Pilih: Setujui atau Tolak',
            'notes.required_if' => 'Alasan penolakan wajib diisi jika dokumen ditolak',
            'notes.max' => 'Catatan maksimal 1000 karakter'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'decision' => 'Keputusan',
            'notes' => 'Catatan'
        ];
    }
}
