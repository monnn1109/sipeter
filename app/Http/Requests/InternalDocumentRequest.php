<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InternalDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Must be logged in and internal user (dosen/staff)
        return auth()->check() && auth()->user()->isInternal();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Address (other info auto-filled from auth user)
            'applicant_address' => 'required|string|max:500',

            // Document Information
            'document_type_id' => [
                'required',
                'exists:document_types,id',
                function ($attribute, $value, $fail) {
                    // Validate document type is applicable for user's role
                    $user = auth()->user();
                    $documentType = \App\Models\DocumentType::find($value);

                    if ($documentType && !$documentType->isApplicableFor($user->role->value)) {
                        $fail('Jenis dokumen ini tidak tersedia untuk ' . $user->role->label());
                    }
                },
            ],
            'quantity' => 'required|integer|min:1|max:10',
            'purpose' => 'required|string|max:500',
            'needed_date' => 'required|date|after:today',

            // Attachment (Optional)
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',

            // 🆕 BARU - Delivery Method
            'delivery_method' => [
                'required',
                Rule::in(['pickup', 'download'])
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            // Address
            'applicant_address.required' => 'Alamat harus diisi',
            'applicant_address.max' => 'Alamat maksimal 500 karakter',

            // Document Information
            'document_type_id.required' => 'Jenis dokumen harus dipilih',
            'document_type_id.exists' => 'Jenis dokumen tidak valid',

            'quantity.required' => 'Jumlah eksemplar harus diisi',
            'quantity.integer' => 'Jumlah eksemplar harus berupa angka',
            'quantity.min' => 'Jumlah minimal 1 eksemplar',
            'quantity.max' => 'Jumlah maksimal 10 eksemplar',

            'purpose.required' => 'Keperluan harus diisi',
            'purpose.max' => 'Keperluan maksimal 500 karakter',

            'needed_date.required' => 'Tanggal dibutuhkan harus diisi',
            'needed_date.date' => 'Format tanggal tidak valid',
            'needed_date.after' => 'Tanggal dibutuhkan minimal besok',

            // Attachment
            'attachment.file' => 'File harus berupa file yang valid',
            'attachment.mimes' => 'File harus berformat PDF, JPG, JPEG, atau PNG',
            'attachment.max' => 'Ukuran file maksimal 2MB',

            // 🆕 BARU - Delivery Method
            'delivery_method.required' => 'Metode pengambilan dokumen harus dipilih',
            'delivery_method.in' => 'Metode pengambilan tidak valid. Pilih Ambil di Kampus atau Download Online',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'applicant_address' => 'alamat',
            'document_type_id' => 'jenis dokumen',
            'quantity' => 'jumlah eksemplar',
            'purpose' => 'keperluan',
            'needed_date' => 'tanggal dibutuhkan',
            'attachment' => 'lampiran',
            'delivery_method' => 'metode pengambilan',
        ];
    }

    /**
     * 🆕 BARU - Prepare data for validation
     */
    protected function prepareForValidation()
    {
        if ($this->has('delivery_method') && is_string($this->delivery_method)) {
            $this->merge([
                'delivery_method' => strtolower(trim($this->delivery_method))
            ]);
        }
    }

    /**
     * 🆕 BARU - Get validated data with user info
     * Helper method to get all data including auto-filled user info
     *
     * @return array
     */
    public function getValidatedDataWithUser(): array
    {
        $user = auth()->user();
        $validated = $this->validated();

        return array_merge($validated, [
            'user_id' => $user->id,
            'applicant_type' => $user->role->value,
            'applicant_name' => $user->name,
            'applicant_identifier' => $user->nip_nidn,
            'applicant_email' => $user->email,
            'applicant_phone' => $user->phone_number, // ← FIXED: phone_number
            'applicant_unit' => $user->unit,
        ]);
    }
}
