<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignatureUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'signature_file' => [
                'required',
                'file',
                'mimes:png,jpg,jpeg,pdf',
                'max:2048',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'signature_file' => 'file tanda tangan',
            'notes' => 'catatan',
        ];
    }

    public function messages(): array
    {
        return [
            'signature_file.required' => 'File tanda tangan wajib diupload.',
            'signature_file.file' => 'File tanda tangan harus berupa file yang valid.',
            'signature_file.mimes' => 'File tanda tangan harus berformat PNG, JPG, atau PDF',
            'signature_file.max' => 'Ukuran file tanda tangan maksimal 2MB.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('notes')) {
            $this->merge([
                'notes' => trim($this->notes),
            ]);
        }
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        \Log::error('❌ SIGNATURE UPLOAD VALIDATION FAILED', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->except(['signature_file']),
            'has_file' => $this->hasFile('signature_file'),
            'file_info' => $this->hasFile('signature_file') ? [
                'name' => $this->file('signature_file')->getClientOriginalName(),
                'size' => $this->file('signature_file')->getSize(),
                'mime' => $this->file('signature_file')->getMimeType(),
            ] : null,
        ]);

        parent::failedValidation($validator);
    }
}
