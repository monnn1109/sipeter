<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\DocumentType;

class MahasiswaDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_type_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $exists = DocumentType::where('id', $value)
                        ->where('is_active', true)
                        ->exists();

                    if (!$exists) {
                        Log::error('❌ Document type validation failed', [
                            'submitted_id' => $value,
                            'available_ids' => DocumentType::where('is_active', true)->pluck('id')->toArray()
                        ]);

                        $fail('Jenis dokumen yang dipilih tidak valid.');
                    }
                },
            ],
            'applicant_name' => 'required|string|max:255',
            'applicant_nim' => 'required|string|max:50',
            'applicant_unit' => 'required|string|max:255',
            'applicant_address' => 'required|string|max:1000',
            'applicant_phone' => 'required|string|min:9|max:15',
            'applicant_email' => 'required|email|max:255',
            'purpose' => 'required|string|max:500',
            'delivery_method' => 'required|string|in:pickup,download',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'document_type_id.required' => 'Pilih jenis dokumen.',
            'document_type_id.integer' => 'Jenis dokumen tidak valid.',
            'applicant_name.required' => 'Nama wajib diisi.',
            'applicant_nim.required' => 'NIM wajib diisi.',
            'applicant_unit.required' => 'Program studi wajib diisi.',
            'applicant_address.required' => 'Alamat wajib diisi.',
            'applicant_phone.required' => 'Nomor HP wajib diisi.',
            'applicant_email.required' => 'Email wajib diisi.',
            'applicant_email.email' => 'Format email tidak valid.',
            'purpose.required' => 'Keperluan wajib diisi.',
            'delivery_method.required' => 'Pilih metode pengambilan.',
        ];
    }

    protected function prepareForValidation()
    {
        $data = [];

        if ($this->has('applicant_phone')) {
            $phone = preg_replace('/[^\d+]/', '', trim($this->input('applicant_phone')));

            if (str_starts_with($phone, '+62')) {
                $phone = substr($phone, 1);
            } elseif (str_starts_with($phone, '62')) {
                if (str_starts_with($phone, '620')) {
                    $phone = '62' . substr($phone, 3);
                }
            } elseif (str_starts_with($phone, '08')) {
                $phone = '62' . substr($phone, 1);
            } elseif (str_starts_with($phone, '8')) {
                $phone = '62' . $phone;
            } elseif (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }

            $data['applicant_phone'] = $phone;
        }

        // ✅ Trim all text inputs
        $data['applicant_name'] = trim($this->input('applicant_name', ''));
        $data['applicant_nim'] = trim($this->input('applicant_nim', ''));
        $data['applicant_email'] = strtolower(trim($this->input('applicant_email', '')));
        $data['applicant_unit'] = trim($this->input('applicant_unit', ''));
        $data['applicant_address'] = trim($this->input('applicant_address', ''));
        $data['purpose'] = trim($this->input('purpose', ''));
        $data['notes'] = $this->input('notes') ? trim($this->input('notes')) : null;

        if ($this->has('document_type_id')) {
            $data['document_type_id'] = (int) $this->input('document_type_id');
        }

        $this->merge($data);

        Log::info('✅ Form data prepared', [
            'document_type_id' => $data['document_type_id'] ?? null,
            'nim' => $data['applicant_nim'] ?? null,
        ]);
    }

    protected function failedValidation(Validator $validator)
    {
        Log::error('❌ Validation Failed', [
            'errors' => $validator->errors()->toArray(),
            'input_doc_type_id' => $this->input('document_type_id'),
        ]);

        parent::failedValidation($validator);
    }
}
