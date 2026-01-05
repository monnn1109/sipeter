<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Services\DocumentDownloadService;
use App\Http\Middleware\CheckDocumentOwner;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    protected DocumentDownloadService $downloadService;

    public function __construct(DocumentDownloadService $downloadService)
    {
        $this->downloadService = $downloadService;
    }

    public function index()
    {
        return view('guest.tracking', [
            'title' => 'Lacak Status Dokumen',
            'active' => 'tracking'
        ]);
    }

    public function check(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:2',
        ], [
            'search.required' => 'Kode dokumen harus diisi',
            'search.min' => 'Kode dokumen minimal 2 karakter',
        ]);

        $search = $request->search;

        $documents = DocumentRequest::with([
                'documentType',
                'verifications',
                'verifications.authority',
                'signatures.authority'
            ])
            ->where('request_code', 'like', "%{$search}%")
            ->orWhere('applicant_name', 'like', "%{$search}%")
            ->orWhere('applicant_identifier', 'like', "%{$search}%")
            ->orWhere('applicant_unit', 'like', "%{$search}%")
            ->orderBy('created_at', 'desc')
            ->get();

        if ($documents->isEmpty()) {
            return view('guest.tracking', [
                'documents' => collect(),
                'search' => $search,
                'title' => 'Hasil Pencarian',
                'active' => 'tracking'
            ]);
        }

        // ✅ REMOVED: Auto-redirect logic yang bikin langsung ke detail
        // ✅ SEKARANG: Selalu tampilkan list hasil pencarian, biar user pilih sendiri

        return view('guest.tracking', [
            'documents' => $documents,
            'search' => $search,
            'title' => 'Hasil Pencarian',
            'active' => 'tracking'
        ]);
    }

    public function show($code)
    {
        // ✅ FIXED: Ganti 'activities.performedBy' jadi 'activities.user'
        $documentRequest = DocumentRequest::with([
                'documentType',
                'activities.user',  // ✅ FIXED: Dari performedBy → user
                'verifications',
                'verifications.authority',
                'signatures.authority'
            ])
            ->where('request_code', $code)
            ->firstOrFail();

        CheckDocumentOwner::setTrackingSession($documentRequest);

        $verificationProgress = [
            'current_step' => $documentRequest->current_verification_step ?? 0,
            'levels' => [
                1 => [
                    'label' => 'Level 1 - Ketua Akademik',
                    'verification' => $documentRequest->verifications->where('verification_level', 1)->first(),
                    'is_current' => $documentRequest->current_verification_step === 1,
                    'is_completed' => $documentRequest->verifications->where('verification_level', 1)->where('status', 'approved')->isNotEmpty(),
                    'is_rejected' => $documentRequest->verifications->where('verification_level', 1)->where('status', 'rejected')->isNotEmpty(),
                ],
                2 => [
                    'label' => 'Level 2 - Wakil Ketua 3',
                    'verification' => $documentRequest->verifications->where('verification_level', 2)->first(),
                    'is_current' => $documentRequest->current_verification_step === 2,
                    'is_completed' => $documentRequest->verifications->where('verification_level', 2)->where('status', 'approved')->isNotEmpty(),
                    'is_rejected' => $documentRequest->verifications->where('verification_level', 2)->where('status', 'rejected')->isNotEmpty(),
                ],
                3 => [
                    'label' => 'Level 3 - Direktur (Final)',
                    'verification' => $documentRequest->verifications->where('verification_level', 3)->first(),
                    'is_current' => $documentRequest->current_verification_step === 3,
                    'is_completed' => $documentRequest->verifications->where('verification_level', 3)->where('status', 'approved')->isNotEmpty(),
                    'is_rejected' => $documentRequest->verifications->where('verification_level', 3)->where('status', 'rejected')->isNotEmpty(),
                ],
            ],
            'is_all_completed' => $documentRequest->current_verification_step === 3
                && $documentRequest->verifications->where('verification_level', 3)->where('status', 'approved')->isNotEmpty(),
            'progress_percentage' => $this->calculateProgressPercentage($documentRequest),
        ];

        $canDownload = false;
        $downloadInfo = null;

        if ($documentRequest->isDownloadDelivery() && $documentRequest->hasUploadedFile()) {
            $validation = $this->downloadService->validateDownload($documentRequest);
            $canDownload = $validation['can_download'];

            if ($canDownload) {
                $downloadInfo = [
                    'file_name' => $documentRequest->getFileName(),
                    'file_size' => $this->downloadService->getFileSize($documentRequest->file_path),
                    'uploaded_at' => $documentRequest->file_uploaded_at?->format('d/m/Y H:i'),
                    'download_url' => route('guest.documents.download', $documentRequest->id),
                    'can_mark_as_taken' => $documentRequest->canBeMarkedAsTaken(),
                    'is_marked_as_taken' => $documentRequest->is_marked_as_taken,
                ];
            }
        }

        return view('guest.tracking-detail', [
            'documentRequest' => $documentRequest,
            'verificationProgress' => $verificationProgress,
            'canDownload' => $canDownload,
            'downloadInfo' => $downloadInfo,
            'title' => 'Detail Tracking - ' . $documentRequest->request_code,
            'active' => 'tracking'
        ]);
    }

    private function calculateProgressPercentage(DocumentRequest $document): int
    {
        $currentStep = $document->current_verification_step ?? 0;

        if ($document->verifications->where('status', 'rejected')->isNotEmpty()) {
            return 0;
        }

        $completedLevels = $document->verifications->where('status', 'approved')->count();

        if ($completedLevels >= 3) {
            return 100;
        } elseif ($completedLevels === 2) {
            return 66;
        } elseif ($completedLevels === 1) {
            return 33;
        }

        return 0;
    }
}
