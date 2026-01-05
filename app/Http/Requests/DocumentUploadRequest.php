<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin can upload files
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // ✅ FIXED: Match dengan controller (document_file bukan file)
            'document_file' => [
                'required',
                'file',
                'mimes:pdf',
                'max:10240', // 10MB
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'document_file' => 'file dokumen',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'document_file.required' => 'File dokumen harus diupload',
            'document_file.file' => 'File yang diupload tidak valid',
            'document_file.mimes' => 'File harus berformat PDF',
            'document_file.max' => 'Ukuran file maksimal 10MB',
        ];
    }

    /**
     * Configure the validator instance.
     * Additional validation for file integrity
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasFile('document_file')) {
                $file = $this->file('document_file');

                // ✅ Validasi MIME type yang benar
                $mimeType = $file->getMimeType();
                $allowedMimes = ['application/pdf', 'application/x-pdf'];

                if (!in_array($mimeType, $allowedMimes)) {
                    $validator->errors()->add('document_file', 'File harus berformat PDF yang valid');
                    return;
                }

                // ✅ Validasi extension
                $extension = strtolower($file->getClientOriginalExtension());
                if ($extension !== 'pdf') {
                    $validator->errors()->add('document_file', 'File harus memiliki ekstensi .pdf');
                    return;
                }

                // ✅ Validasi ukuran file (double check)
                $maxSize = 10 * 1024 * 1024; // 10MB in bytes
                if ($file->getSize() > $maxSize) {
                    $validator->errors()->add('document_file', 'Ukuran file melebihi batas maksimal 10MB');
                    return;
                }

                // ✅ Validasi file dapat dibaca
                if (!$file->isValid()) {
                    $validator->errors()->add('document_file', 'File rusak atau tidak dapat dibaca');
                    return;
                }
            }
        });
    }

    /**
     * ✅ Handle failed validation untuk JSON response
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        if ($this->expectsJson()) {
            throw new \Illuminate\Validation\ValidationException(
                $validator,
                response()->json([
                    'success' => false,
                    'message' => 'Validasi file gagal',
                    'errors' => $validator->errors()
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }

    /**
     * ✅ Get file size in human readable format
     */
    public function getFileSizeFormatted(): ?string
    {
        if (!$this->hasFile('document_file')) {
            return null;
        }

        $bytes = $this->file('document_file')->getSize();
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * ✅ Get original filename (sanitized)
     */
    public function getSanitizedFilename(): ?string
    {
        if (!$this->hasFile('document_file')) {
            return null;
        }

        $originalName = $this->file('document_file')->getClientOriginalName();

        // Remove extension
        $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);

        // Sanitize: keep only alphanumeric, dash, underscore, and space
        $sanitized = preg_replace('/[^a-zA-Z0-9\-\_\s]/', '', $nameWithoutExt);

        // Replace multiple spaces with single space
        $sanitized = preg_replace('/\s+/', ' ', $sanitized);

        // Trim and limit length
        $sanitized = trim($sanitized);
        $sanitized = substr($sanitized, 0, 100);

        return $sanitized . '.pdf';
    }
}
