<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignatureVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller/middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $action = $this->input('action', 'verify');

        $rules = [
            'signature_id' => [
                'required',
                'exists:document_signatures,id',
            ],
            'action' => [
                'required',
                'string',
                'in:verify,reject',
            ],
        ];

        // Add conditional rules based on action
        if ($action === 'verify') {
            $rules['verification_notes'] = [
                'nullable',
                'string',
                'max:1000',
            ];
        }

        if ($action === 'reject') {
            $rules['rejection_reason'] = [
                'required',
                'string',
                'min:10',
                'max:1000',
            ];
        }

        return $rules;
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'signature_id' => 'ID tanda tangan',
            'action' => 'aksi',
            'verification_notes' => 'catatan verifikasi',
            'rejection_reason' => 'alasan penolakan',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'signature_id.required' => 'ID tanda tangan tidak valid.',
            'signature_id.exists' => 'Data tanda tangan tidak ditemukan.',

            'action.required' => 'Aksi verifikasi wajib dipilih.',
            'action.in' => 'Aksi harus berupa "verify" atau "reject".',

            'verification_notes.max' => 'Catatan verifikasi maksimal 1000 karakter.',

            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ];
    }

    /**
     * Prepare data for validation
     */
    protected function prepareForValidation(): void
    {
        // Clean up any extra spaces
        if ($this->has('verification_notes')) {
            $this->merge([
                'verification_notes' => trim($this->verification_notes),
            ]);
        }

        if ($this->has('rejection_reason')) {
            $this->merge([
                'rejection_reason' => trim($this->rejection_reason),
            ]);
        }

        // Ensure action is lowercase
        if ($this->has('action')) {
            $this->merge([
                'action' => strtolower($this->action),
            ]);
        }
    }


    public function getVerifyData(): array
    {
        return [
            'signature_id' => $this->signature_id,
            'notes' => $this->input('verification_notes'),
        ];
    }


    public function getRejectData(): array
    {
        return [
            'signature_id' => $this->signature_id,
            'reason' => $this->rejection_reason,
        ];
    }


    public function isVerifyAction(): bool
    {
        return $this->action === 'verify';
    }


    public function isRejectAction(): bool
    {
        return $this->action === 'reject';
    }
}
