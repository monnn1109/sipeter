<?php

namespace App\Services;

use App\Models\{DocumentSignature, User};
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SignatureVerificationService
{

    public function verifyFileIntegrity(DocumentSignature $signature): array
    {
        $results = [
            'valid' => true,
            'checks' => [],
            'errors' => [],
        ];

        $fileExists = $signature->hasSignatureFile();
        $results['checks']['file_exists'] = $fileExists;

        if (!$fileExists) {
            $results['valid'] = false;
            $results['errors'][] = 'File TTD tidak ditemukan';
            return $results;
        }

        $fileSize = $signature->file_size;
        $results['checks']['file_size'] = $fileSize;

        if ($fileSize > (5 * 1024 * 1024)) {
            $results['valid'] = false;
            $results['errors'][] = 'Ukuran file terlalu besar (max 5MB)';
        }

        if ($fileSize < 1024) {
            $results['valid'] = false;
            $results['errors'][] = 'Ukuran file terlalu kecil (min 1KB)';
        }

        $fileType = $signature->file_type;
        $results['checks']['file_type'] = $fileType;

        $allowedTypes = ['png', 'pdf'];
        if (!in_array(strtolower($fileType), $allowedTypes)) {
            $results['valid'] = false;
            $results['errors'][] = 'Tipe file tidak valid. Harus PNG atau PDF';
        }

        try {
            $filePath = Storage::disk('signatures')->path($signature->signature_file);
            $results['checks']['file_readable'] = is_readable($filePath);

            if (!is_readable($filePath)) {
                $results['valid'] = false;
                $results['errors'][] = 'File tidak dapat dibaca';
            }
        } catch (\Exception $e) {
            $results['valid'] = false;
            $results['errors'][] = 'Gagal mengakses file';
        }

        if (strtolower($fileType) === 'png') {
            $imageCheck = $this->verifyImageFile($signature);
            $results['checks']['valid_image'] = $imageCheck['valid'];

            if (!$imageCheck['valid']) {
                $results['valid'] = false;
                $results['errors'] = array_merge($results['errors'], $imageCheck['errors']);
            }
        }

        return $results;
    }
    protected function verifyImageFile(DocumentSignature $signature): array
    {
        $results = [
            'valid' => true,
            'errors' => [],
        ];

        try {
            $filePath = Storage::disk('signatures')->path($signature->signature_file);

            $imageInfo = @getimagesize($filePath);

            if ($imageInfo === false) {
                $results['valid'] = false;
                $results['errors'][] = 'File PNG tidak valid atau corrupt';
                return $results;
            }

            list($width, $height) = $imageInfo;

            if ($width < 100 || $height < 50) {
                $results['valid'] = false;
                $results['errors'][] = 'Ukuran gambar terlalu kecil (min 100x50 pixels)';
            }

            if ($width > 4000 || $height > 4000) {
                $results['valid'] = false;
                $results['errors'][] = 'Ukuran gambar terlalu besar (max 4000x4000 pixels)';
            }

            if ($imageInfo[2] !== IMAGETYPE_PNG) {
                $results['valid'] = false;
                $results['errors'][] = 'File bukan format PNG yang valid';
            }

        } catch (\Exception $e) {
            $results['valid'] = false;
            $results['errors'][] = 'Gagal memverifikasi gambar: ' . $e->getMessage();
        }

        return $results;
    }

    public function checkQualityStandards(DocumentSignature $signature): array
    {
        $results = [
            'passed' => true,
            'score' => 0,
            'checks' => [],
            'warnings' => [],
        ];

        $maxScore = 100;
        $currentScore = 0;

        $fileSize = $signature->file_size;
        if ($fileSize >= 50000 && $fileSize <= 2000000) {
            $currentScore += 30;
            $results['checks']['file_size'] = 'optimal';
        } elseif ($fileSize < 50000) {
            $currentScore += 15;
            $results['checks']['file_size'] = 'small';
            $results['warnings'][] = 'File size mungkin terlalu kecil untuk kualitas optimal';
        } else {
            $currentScore += 20;
            $results['checks']['file_size'] = 'large';
            $results['warnings'][] = 'File size besar, tapi masih dalam batas';
        }

        $uploadTime = $signature->uploaded_at;
        $requestTime = $signature->requested_at;

        if ($uploadTime && $requestTime) {
            $responseTime = $uploadTime->diffInHours($requestTime);

            if ($responseTime <= 2) {
                $currentScore += 20;
                $results['checks']['response_time'] = 'excellent';
            } elseif ($responseTime <= 24) {
                $currentScore += 15;
                $results['checks']['response_time'] = 'good';
            } else {
                $currentScore += 10;
                $results['checks']['response_time'] = 'acceptable';
            }
        }

        if (strtolower($signature->file_type) === 'pdf') {
            $currentScore += 20;
            $results['checks']['file_type'] = 'preferred';
        } else {
            $currentScore += 15;
            $results['checks']['file_type'] = 'acceptable';
        }

        $metadataScore = 0;

        if (!empty($signature->uploaded_from)) {
            $metadataScore += 10;
        }

        if (!empty($signature->metadata)) {
            $metadataScore += 10;
        }

        if ($signature->hasSignatureFile()) {
            $metadataScore += 10;
        }

        $currentScore += $metadataScore;
        $results['checks']['metadata'] = $metadataScore >= 20 ? 'complete' : 'partial';

        $results['score'] = round(($currentScore / $maxScore) * 100);

        $results['passed'] = $results['score'] >= 60;

        if ($results['score'] < 60) {
            $results['warnings'][] = 'Kualitas signature di bawah standar minimum';
        }

        return $results;
    }

    public function generateVerificationReport(
        DocumentSignature $signature,
        User $verifier
    ): array {
        $integrityCheck = $this->verifyFileIntegrity($signature);
        $qualityCheck = $this->checkQualityStandards($signature);

        $report = [
            'signature_id' => $signature->id,
            'document_code' => $signature->documentRequest->request_code,
            'authority_name' => $signature->signatureAuthority->name,
            'authority_type' => $signature->signatureAuthority->authority_type->value,
            'file_info' => [
                'name' => basename($signature->signature_file),
                'type' => $signature->file_type,
                'size' => $signature->file_size,
                'size_formatted' => $signature->file_size_formatted,
            ],
            'timestamps' => [
                'requested' => $signature->requested_at?->format('Y-m-d H:i:s'),
                'uploaded' => $signature->uploaded_at?->format('Y-m-d H:i:s'),
                'verified' => $signature->verified_at?->format('Y-m-d H:i:s'),
            ],
            'integrity_check' => $integrityCheck,
            'quality_check' => $qualityCheck,
            'verifier' => [
                'id' => $verifier->id,
                'name' => $verifier->name,
            ],
            'verification_time' => now()->format('Y-m-d H:i:s'),
            'recommendation' => $this->generateRecommendation($integrityCheck, $qualityCheck),
        ];

        return $report;
    }

    protected function generateRecommendation(array $integrityCheck, array $qualityCheck): array
    {
        $recommendation = [
            'action' => 'approve',
            'confidence' => 'high',
            'reason' => '',
            'notes' => [],
        ];

        if (!$integrityCheck['valid']) {
            $recommendation['action'] = 'reject';
            $recommendation['confidence'] = 'high';
            $recommendation['reason'] = 'File gagal verifikasi integritas';
            $recommendation['notes'] = $integrityCheck['errors'];
            return $recommendation;
        }

        if (!$qualityCheck['passed']) {
            $recommendation['action'] = 'review';
            $recommendation['confidence'] = 'medium';
            $recommendation['reason'] = 'Kualitas file di bawah standar';
            $recommendation['notes'] = $qualityCheck['warnings'];
            return $recommendation;
        }

        if ($qualityCheck['score'] >= 90) {
            $recommendation['action'] = 'approve';
            $recommendation['confidence'] = 'high';
            $recommendation['reason'] = 'File memenuhi semua standar kualitas';
        } elseif ($qualityCheck['score'] >= 75) {
            $recommendation['action'] = 'approve';
            $recommendation['confidence'] = 'high';
            $recommendation['reason'] = 'File memenuhi standar kualitas';
        } else {
            $recommendation['action'] = 'review';
            $recommendation['confidence'] = 'medium';
            $recommendation['reason'] = 'File memerlukan review tambahan';
            $recommendation['notes'] = $qualityCheck['warnings'];
        }

        return $recommendation;
    }
    public function batchVerify(array $signatureIds): array
    {
        $results = [
            'total' => count($signatureIds),
            'passed' => 0,
            'failed' => 0,
            'details' => [],
        ];

        foreach ($signatureIds as $signatureId) {
            $signature = DocumentSignature::find($signatureId);

            if (!$signature) {
                $results['failed']++;
                $results['details'][] = [
                    'signature_id' => $signatureId,
                    'status' => 'not_found',
                ];
                continue;
            }

            $verification = $this->verifyFileIntegrity($signature);

            if ($verification['valid']) {
                $results['passed']++;
            } else {
                $results['failed']++;
            }

            $results['details'][] = [
                'signature_id' => $signatureId,
                'document_code' => $signature->documentRequest->request_code,
                'authority_name' => $signature->signatureAuthority->name,
                'status' => $verification['valid'] ? 'passed' : 'failed',
                'errors' => $verification['errors'],
            ];
        }

        return $results;
    }
    public function logVerification(
        DocumentSignature $signature,
        User $verifier,
        string $action,
        array $data = []
    ): void {
        Log::info('Signature verification', [
            'signature_id' => $signature->id,
            'document_code' => $signature->documentRequest->request_code,
            'verifier_id' => $verifier->id,
            'verifier_name' => $verifier->name,
            'action' => $action,
            'data' => $data,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
