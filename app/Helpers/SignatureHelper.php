<?php

namespace App\Helpers;

use App\Models\DocumentRequest;
use App\Models\SignatureAuthority;
use App\Enums\SignatureStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SignatureHelper
{

    public static function validateSignatureFile(UploadedFile $file): array
    {
        $errors = [];
        $config = config('services.signature_authority.signature_settings');

        $extension = strtolower($file->getClientOriginalExtension());
        $allowedTypes = $config['allowed_types'] ?? ['png', 'pdf'];

        if (!in_array($extension, $allowedTypes)) {
            $errors[] = "File harus berformat: " . implode(', ', $allowedTypes);
        }

        $maxSize = $config['max_file_size'] ?? 5120;
        $fileSizeKB = $file->getSize() / 1024;

        if ($fileSizeKB > $maxSize) {
            $maxSizeMB = $maxSize / 1024;
            $errors[] = "Ukuran file maksimal {$maxSizeMB}MB";
        }

        $mimeType = $file->getMimeType();
        $allowedMimes = [
            'image/png',
            'application/pdf',
        ];

        if (!in_array($mimeType, $allowedMimes)) {
            $errors[] = "Tipe file tidak valid";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'file_info' => [
                'name' => $file->getClientOriginalName(),
                'extension' => $extension,
                'size' => $fileSizeKB,
                'mime_type' => $mimeType,
            ],
        ];
    }


    public static function storeSignatureFile(
        UploadedFile $file,
        DocumentRequest $documentRequest,
        SignatureAuthority $authority
    ): array {
        $validation = self::validateSignatureFile($file);

        if (!$validation['valid']) {
            return [
                'success' => false,
                'errors' => $validation['errors'],
            ];
        }

        try {
            $extension = $file->getClientOriginalExtension();
            $filename = self::generateSignatureFilename($documentRequest, $authority, $extension);

            $path = $file->storeAs(
                self::getSignatureDirectory($documentRequest),
                $filename,
                'signatures'
            );

            return [
                'success' => true,
                'path' => $path,
                'file_type' => $extension,
                'file_size' => $file->getSize(),
                'original_name' => $file->getClientOriginalName(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => ['Gagal menyimpan file: ' . $e->getMessage()],
            ];
        }
    }


    public static function generateSignatureFilename(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        string $extension
    ): string {
        $authorityType = $authority->authority_type->value;
        $code = $documentRequest->code;
        $timestamp = now()->format('YmdHis');
        $random = Str::random(6);

        return "{$code}_{$authorityType}_{$timestamp}_{$random}.{$extension}";
    }


    public static function getSignatureDirectory(DocumentRequest $documentRequest): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');

        return "{$year}/{$month}/{$documentRequest->id}";
    }

    public static function deleteSignatureFile(string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        try {
            return Storage::disk('signatures')->delete($path);
        } catch (\Exception $e) {
            return false;
        }
    }


    public static function areAllSignaturesCompleted(DocumentRequest $documentRequest): bool
    {
        $signatures = $documentRequest->signatures;

        if ($signatures->isEmpty()) {
            return false;
        }

        return $signatures->every(function ($signature) {
            return $signature->status === SignatureStatus::VERIFIED;
        });
    }


    public static function getSignatureCompletionPercentage(DocumentRequest $documentRequest): float
    {
        $signatures = $documentRequest->signatures;
        $total = $signatures->count();

        if ($total === 0) {
            return 0;
        }

        $completed = $signatures->where('status', SignatureStatus::VERIFIED)->count();

        return round(($completed / $total) * 100, 2);
    }


    public static function getPendingSignaturesCount(DocumentRequest $documentRequest): int
    {
        return $documentRequest->signatures()
            ->whereIn('status', [SignatureStatus::REQUESTED, SignatureStatus::UPLOADED])
            ->count();
    }


    public static function getSignatureSummary(DocumentRequest $documentRequest): array
    {
        $signatures = $documentRequest->signatures;

        return [
            'total' => $signatures->count(),
            'requested' => $signatures->where('status', SignatureStatus::REQUESTED)->count(),
            'uploaded' => $signatures->where('status', SignatureStatus::UPLOADED)->count(),
            'verified' => $signatures->where('status', SignatureStatus::VERIFIED)->count(),
            'rejected' => $signatures->where('status', SignatureStatus::REJECTED)->count(),
            'completion_percentage' => self::getSignatureCompletionPercentage($documentRequest),
            'all_completed' => self::areAllSignaturesCompleted($documentRequest),
        ];
    }


    public static function formatFileSize(int $bytes): string
    {
        $kb = $bytes / 1024;

        if ($kb < 1024) {
            return round($kb, 2) . ' KB';
        }

        $mb = $kb / 1024;
        return round($mb, 2) . ' MB';
    }


    public static function getFileIcon(string $extension): string
    {
        return match (strtolower($extension)) {
            'pdf' => 'file-text',
            'png', 'jpg', 'jpeg' => 'image',
            default => 'file',
        };
    }


    public static function isSignatureOverdue($signature, int $hoursLimit = 24): bool
    {
        if (!$signature->requested_at || $signature->isFinal()) {
            return false;
        }

        return $signature->requested_at->addHours($hoursLimit)->isPast();
    }

    public static function getOverdueSignatures(int $hoursLimit = 24)
    {
        $cutoffTime = now()->subHours($hoursLimit);

        return \App\Models\DocumentSignature::whereIn('status', [
                SignatureStatus::REQUESTED,
                SignatureStatus::UPLOADED
            ])
            ->where('requested_at', '<=', $cutoffTime)
            ->with(['documentRequest', 'signatureAuthority'])
            ->get();
    }


    public static function canUploadSignature($signature): array
    {
        if (!$signature->canBeUploaded()) {
            return [
                'can_upload' => false,
                'reason' => 'Status saat ini tidak memungkinkan untuk upload',
            ];
        }

        if (!$signature->signatureAuthority->is_active) {
            return [
                'can_upload' => false,
                'reason' => 'Authority tidak aktif',
            ];
        }

        return [
            'can_upload' => true,
            'reason' => null,
        ];
    }


    public static function canVerifySignature($signature): array
    {
        if (!$signature->canBeVerified()) {
            return [
                'can_verify' => false,
                'reason' => 'Signature belum diupload atau sudah diverifikasi',
            ];
        }

        if (!$signature->hasSignatureFile()) {
            return [
                'can_verify' => false,
                'reason' => 'File signature tidak ditemukan',
            ];
        }

        return [
            'can_verify' => true,
            'reason' => null,
        ];
    }


    public static function getStatusBadgeHtml(SignatureStatus $status): string
    {
        $label = $status->label();
        $color = $status->color();
        $icon = $status->icon();

        return "
            <span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{$color}-100 text-{$color}-800'>
                <i data-lucide='{$icon}' class='w-3 h-3 mr-1'></i>
                {$label}
            </span>
        ";
    }
}
