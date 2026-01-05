<?php

namespace App\Services;

use App\Models\DocumentRequest;
use Illuminate\Support\Facades\Storage;

class DocumentDownloadService
{
    public function validateDownload(DocumentRequest $documentRequest): array
    {
        $deliveryMethodValue = ($documentRequest->delivery_method instanceof \App\Enums\DeliveryMethod)
            ? $documentRequest->delivery_method->value
            : $documentRequest->delivery_method;

        if ($deliveryMethodValue !== 'download') {
            return [
                'can_download' => false,
                'message' => 'Dokumen ini menggunakan metode pengambilan Pickup, tidak bisa didownload.'
            ];
        }

        $statusValue = ($documentRequest->status instanceof \App\Enums\DocumentStatus)
            ? $documentRequest->status->value
            : $documentRequest->status;

        if (!in_array($statusValue, ['ready_for_pickup', 'picked_up', 'completed'])) {
            $statusLabel = ($documentRequest->status instanceof \App\Enums\DocumentStatus)
                ? $documentRequest->status->label()
                : ucfirst(str_replace('_', ' ', $statusValue));

            return [
                'can_download' => false,
                'message' => 'Dokumen belum siap untuk didownload. Status: ' . $statusLabel
            ];
        }

        if (empty($documentRequest->file_path)) {
            return [
                'can_download' => false,
                'message' => 'File dokumen belum diupload oleh admin.'
            ];
        }

        if (!Storage::disk('documents')->exists($documentRequest->file_path)) {
            \Log::warning('File not found in documents disk', [
                'file_path' => $documentRequest->file_path,
                'document_id' => $documentRequest->id,
                'full_path' => Storage::disk('documents')->path($documentRequest->file_path)
            ]);

            return [
                'can_download' => false,
                'message' => 'File tidak ditemukan di server. Silakan hubungi admin.'
            ];
        }

        return [
            'can_download' => true,
            'message' => 'Dokumen siap untuk didownload',
            'can_mark_as_taken' => $documentRequest->canBeMarkedAsTaken(),
            'is_marked_as_taken' => $documentRequest->is_marked_as_taken,
            'marked_at' => $documentRequest->marked_as_taken_at,
        ];
    }

    public function downloadFile(DocumentRequest $documentRequest): array
    {
        try {
            $filePath = $documentRequest->file_path;

            if (!Storage::disk('documents')->exists($filePath)) {
                \Log::error('Download failed - file not found', [
                    'file_path' => $filePath,
                    'document_id' => $documentRequest->id
                ]);

                return [
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ];
            }

            $fileName = $this->generateDownloadFileName($documentRequest);
            $response = Storage::disk('documents')->download($filePath, $fileName);

            return [
                'success' => true,
                'response' => $response,
                'file_name' => $fileName
            ];

        } catch (\Exception $e) {
            \Log::error('Download File Error', [
                'error' => $e->getMessage(),
                'file_path' => $documentRequest->file_path ?? 'N/A',
                'document_id' => $documentRequest->id
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengunduh file: ' . $e->getMessage()
            ];
        }
    }

    public function getFileSize(string $filePath): ?string
    {
        try {
            if (!Storage::disk('documents')->exists($filePath)) {
                return null;
            }

            $bytes = Storage::disk('documents')->size($filePath);
            return $this->formatBytes($bytes);

        } catch (\Exception $e) {
            \Log::warning('Failed to get file size', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function generateDownloadFileName(DocumentRequest $documentRequest): string
    {
        $requestCode = str_replace('/', '-', $documentRequest->request_code);
        $documentType = str_replace(' ', '_', $documentRequest->documentType->name);
        $originalExtension = pathinfo($documentRequest->file_path, PATHINFO_EXTENSION);

        return "{$requestCode}_{$documentType}.{$originalExtension}";
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function isDownloadable(DocumentRequest $documentRequest): bool
    {
        $deliveryMethodValue = ($documentRequest->delivery_method instanceof \App\Enums\DeliveryMethod)
            ? $documentRequest->delivery_method->value
            : $documentRequest->delivery_method;

        $statusValue = ($documentRequest->status instanceof \App\Enums\DocumentStatus)
            ? $documentRequest->status->value
            : $documentRequest->status;

        return $deliveryMethodValue === 'download'
            && in_array($statusValue, ['ready_for_pickup', 'picked_up', 'completed'])
            && !empty($documentRequest->file_path)
            && Storage::disk('documents')->exists($documentRequest->file_path);
    }

    public function getDownloadStats(DocumentRequest $documentRequest): array
    {
        return [
            'total_downloads' => $documentRequest->activities()
                ->where('activity_type', 'downloaded')
                ->count(),
            'last_downloaded_at' => $documentRequest->activities()
                ->where('activity_type', 'downloaded')
                ->latest()
                ->value('created_at'),
            'file_size' => $this->getFileSize($documentRequest->file_path),
            'is_marked_as_taken' => $documentRequest->is_marked_as_taken,
            'can_mark_as_taken' => $documentRequest->canBeMarkedAsTaken(),
            'marked_at' => $documentRequest->marked_as_taken_at,
        ];
    }
}
