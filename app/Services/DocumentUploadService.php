<?php

namespace App\Services;

use App\Models\DocumentRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentUploadService
{
    public function upload(DocumentRequest $documentRequest, UploadedFile $file): array
    {
        try {
            if ($documentRequest->hasFile()) {
                return [
                    'success' => false,
                    'message' => 'Dokumen sudah memiliki file. Gunakan fitur Replace untuk mengganti.'
                ];
            }

            if ($documentRequest->requiresSignature() && !$documentRequest->areAllSignaturesVerified()) {
                return [
                    'success' => false,
                    'message' => 'Tidak bisa upload dokumen. Tanda tangan digital belum lengkap. (' .
                                 $documentRequest->signatures_completed . '/' .
                                 $documentRequest->signatures_required . ' selesai)'
                ];
            }

            $fileName = $this->generateFileName($file, $documentRequest);

            $folder = $documentRequest->applicant_type->value === 'mahasiswa'
                ? 'mahasiswa'
                : 'internal';

            $filePath = $file->storeAs($folder, $fileName, 'documents');

            if (!$filePath) {
                throw new \Exception('Gagal menyimpan file ke storage');
            }

            $documentRequest->update([
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_uploaded_at' => now(),
                'uploaded_by' => auth()->id(),
                'status' => 'ready_for_pickup',
                'ready_at' => now(),
            ]);

            Log::info('Document uploaded successfully', [
                'document_id' => $documentRequest->id,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'uploaded_by' => auth()->id()
            ]);

            return [
                'success' => true,
                'message' => 'File dokumen berhasil diupload!',
                'file_path' => $filePath,
                'file_name' => $fileName,
            ];

        } catch (\Exception $e) {
            Log::error('Upload file failed', [
                'document_id' => $documentRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengupload file: ' . $e->getMessage()
            ];
        }
    }

    public function replace(DocumentRequest $documentRequest, UploadedFile $file): array
    {
        try {
            if (!$documentRequest->hasFile()) {
                return [
                    'success' => false,
                    'message' => 'Tidak ada file yang dapat diganti.'
                ];
            }

            $oldFilePath = $documentRequest->file_path;

            if (Storage::disk('documents')->exists($oldFilePath)) {
                Storage::disk('documents')->delete($oldFilePath);
            }

            $fileName = $this->generateFileName($file, $documentRequest);

            $folder = $documentRequest->applicant_type->value === 'mahasiswa'
                ? 'mahasiswa'
                : 'internal';

            $filePath = $file->storeAs($folder, $fileName, 'documents');

            if (!$filePath) {
                throw new \Exception('Gagal menyimpan file baru ke storage');
            }

            $documentRequest->update([
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_uploaded_at' => now(),
                'uploaded_by' => auth()->id(),
            ]);

            Log::info('Document replaced successfully', [
                'document_id' => $documentRequest->id,
                'old_file' => $oldFilePath,
                'new_file' => $filePath,
                'replaced_by' => auth()->id()
            ]);

            return [
                'success' => true,
                'message' => 'File dokumen berhasil diganti!',
                'file_path' => $filePath,
                'file_name' => $fileName,
            ];

        } catch (\Exception $e) {
            Log::error('Replace file failed', [
                'document_id' => $documentRequest->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengganti file: ' . $e->getMessage()
            ];
        }
    }

    public function delete(DocumentRequest $documentRequest): array
    {
        try {
            if (!$documentRequest->hasFile()) {
                return [
                    'success' => false,
                    'message' => 'Tidak ada file yang dapat dihapus.'
                ];
            }

            $oldFilePath = $documentRequest->file_path;
            $oldFileName = $documentRequest->file_name ?? basename($oldFilePath);

            if (Storage::disk('documents')->exists($oldFilePath)) {
                Storage::disk('documents')->delete($oldFilePath);
            }

            $documentRequest->update([
                'file_path' => null,
                'file_name' => null,
                'file_uploaded_at' => null,
                'uploaded_by' => null,
            ]);

            Log::info('Document file deleted', [
                'document_id' => $documentRequest->id,
                'file_name' => $oldFileName,
                'deleted_by' => auth()->id()
            ]);

            return [
                'success' => true,
                'message' => 'File dokumen berhasil dihapus!'
            ];

        } catch (\Exception $e) {
            Log::error('Delete file failed', [
                'document_id' => $documentRequest->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal menghapus file: ' . $e->getMessage()
            ];
        }
    }

    public function getFileInfo(DocumentRequest $documentRequest): ?array
    {
        if (!$documentRequest->hasFile()) {
            return null;
        }

        $filePath = $documentRequest->file_path;

        try {
            $exists = Storage::disk('documents')->exists($filePath);
            $size = $exists ? Storage::disk('documents')->size($filePath) : 0;
            $mimeType = $exists ? Storage::disk('documents')->mimeType($filePath) : null;

            return [
                'file_name' => $documentRequest->file_name ?? basename($filePath),
                'file_path' => $filePath,
                'file_size' => $size,
                'file_size_human' => $this->formatBytes($size),
                'mime_type' => $mimeType,
                'uploaded_at' => $documentRequest->file_uploaded_at,
                'uploaded_by' => $documentRequest->uploadedBy?->name,
                'exists' => $exists,
            ];
        } catch (\Exception $e) {
            Log::error('Get file info failed', [
                'document_id' => $documentRequest->id,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    public function canUploadFile(DocumentRequest $documentRequest): array
    {
        if ($documentRequest->hasFile()) {
            return [
                'can_upload' => false,
                'reason' => 'Dokumen sudah memiliki file'
            ];
        }

        if ($documentRequest->requiresSignature()) {
            if (!$documentRequest->areAllSignaturesVerified()) {
                return [
                    'can_upload' => false,
                    'reason' => 'Tanda tangan digital belum lengkap (' .
                               $documentRequest->signatures_completed . '/' .
                               $documentRequest->signatures_required . ')',
                    'signatures_completed' => $documentRequest->signatures_completed,
                    'signatures_required' => $documentRequest->signatures_required,
                ];
            }
        }

        return [
            'can_upload' => true,
            'reason' => null
        ];
    }

    private function generateFileName(UploadedFile $file, DocumentRequest $documentRequest): string
    {
        $extension = $file->getClientOriginalExtension();
        $requestCode = str_replace(['/', ' '], '-', $documentRequest->request_code);
        $timestamp = now()->format('YmdHis');
        $random = substr(md5(uniqid()), 0, 6);

        return "{$requestCode}_{$timestamp}_{$random}.{$extension}";
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        if ($bytes === 0) {
            return '0 Bytes';
        }

        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), $precision) . ' ' . $sizes[$i];
    }

    public function validateFile(UploadedFile $file): array
    {
        $errors = [];

        if ($file->getMimeType() !== 'application/pdf') {
            $errors[] = 'File harus berformat PDF';
        }

        $maxSize = 10 * 1024 * 1024;
        if ($file->getSize() > $maxSize) {
            $errors[] = 'Ukuran file maksimal 10MB';
        }

        if (!$file->isValid()) {
            $errors[] = 'File tidak valid atau corrupt';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
