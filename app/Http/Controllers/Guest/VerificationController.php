<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentVerificationRequest;
use App\Services\DocumentVerificationService;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct(
        private DocumentVerificationService $verificationService
    ) {}

    public function show(string $token)
    {
        $verification = $this->verificationService->getByToken($token);

        if (!$verification) {
            return view('guest.verification-invalid', [
                'message' => 'Link verifikasi tidak valid atau sudah expired. Silakan hubungi admin jika ada pertanyaan.'
            ]);
        }

        $verification->load([
            'documentRequest.documentType',
            'documentRequest.verifications',
            'authority'
        ]);

        $currentLevel = $verification->verification_level;

        $previousApprovals = $verification->documentRequest->verifications()
            ->where('verification_level', '<', $currentLevel)
            ->where('status', 'approved')
            ->with('authority')
            ->orderBy('verification_level', 'asc')
            ->get();

        $progressMap = [1 => 0, 2 => 33, 3 => 66];
        $currentProgress = $progressMap[$currentLevel] ?? 0;

        $levelLabels = [
            1 => 'Level 1 dari 3 - Ketua Akademik',
            2 => 'Level 2 dari 3 - Wakil Ketua 3 (Kemahasiswaan)',
            3 => 'Level 3 dari 3 - Direktur (FINAL)'
        ];

        return view('guest.verification', [
            'verification' => $verification,
            'currentLevel' => $currentLevel,
            'levelLabel' => $levelLabels[$currentLevel] ?? "Level {$currentLevel}",
            'previousApprovals' => $previousApprovals,
            'currentProgress' => $currentProgress,
            'isFinalLevel' => $currentLevel === 3,
        ]);
    }

    public function submit(DocumentVerificationRequest $request, string $token)
    {
        $verification = $this->verificationService->getByToken($token);

        if (!$verification) {
            return view('guest.verification-invalid', [
                'message' => 'Link verifikasi sudah tidak valid atau expired.'
            ]);
        }

        $validated = $request->validated();
        $currentLevel = $verification->verification_level;

        try {
            if ($validated['decision'] === 'approved') {
                // ✅ FIXED: Hanya 2 parameter (verification, notes)
                $this->verificationService->approveLevel(
                    $verification,
                    $validated['notes'] ?? null
                );

                $nextStepMap = [
                    1 => 'Level 2 (Wakil Ketua 3)',
                    2 => 'Level 3 (Direktur - Final)',
                    3 => 'Penandatanganan (TTD)'
                ];

                $progressAfterApprove = [1 => 33, 2 => 66, 3 => 100];

                return view('guest.verification-success', [
                    'type' => 'approved',
                    'document' => $verification->documentRequest->fresh(),
                    'authority' => $verification->authority,
                    'currentLevel' => $currentLevel,
                    'progressPercentage' => $progressAfterApprove[$currentLevel] ?? 100,
                    'nextStep' => $nextStepMap[$currentLevel] ?? 'Selesai',
                    'isFinalLevel' => $currentLevel === 3,
                ]);

            } else {
                // ✅ FIXED: Hanya 2 parameter (verification, reason)
                $this->verificationService->rejectLevel(
                    $verification,
                    $validated['notes']
                );

                $levelLabels = [
                    1 => 'Level 1 (Ketua Akademik)',
                    2 => 'Level 2 (Wakil Ketua 3)',
                    3 => 'Level 3 (Direktur - Final)'
                ];

                return view('guest.verification-success', [
                    'type' => 'rejected',
                    'document' => $verification->documentRequest->fresh(),
                    'authority' => $verification->authority,
                    'reason' => $validated['notes'],
                    'currentLevel' => $currentLevel,
                    'levelLabel' => $levelLabels[$currentLevel] ?? "Level {$currentLevel}",
                ]);
            }

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal memproses verifikasi: ' . $e->getMessage())
                ->withInput();
        }
    }
}
