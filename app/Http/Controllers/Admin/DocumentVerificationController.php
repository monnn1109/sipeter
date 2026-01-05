<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{DocumentRequest, DocumentVerification, SignatureAuthority};
use App\Services\{DocumentVerificationService, NotificationService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentVerificationController extends Controller
{
    protected $verificationService;
    protected $notificationService;

    public function __construct(
        DocumentVerificationService $verificationService,
        NotificationService $notificationService
    ) {
        $this->verificationService = $verificationService;
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $stats = [
            'total' => DocumentVerification::count(),
            'pending' => DocumentVerification::where('status', 'pending')->count(),
            'approved' => DocumentVerification::where('status', 'approved')->count(),
            'rejected' => DocumentVerification::where('status', 'rejected')->count(),
            'by_level' => [
                'level_1' => DocumentVerification::where('verification_level', 1)->count(),
                'level_2' => DocumentVerification::where('verification_level', 2)->count(),
                'level_3' => DocumentVerification::where('verification_level', 3)->count(),
            ],
        ];

        $status = $request->get('status', 'all');
        $level = $request->get('level', 'all');

        $query = DocumentVerification::with([
            'documentRequest.documentType',
            'documentRequest.user',
            'authority'
        ])->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($level !== 'all' && in_array($level, [1, 2, 3])) {
            $query->where('verification_level', $level);
        }

        $verifications = $query->paginate(20);

        return view('admin.verifications.index', compact('verifications', 'stats', 'status', 'level'));
    }

    public function pending(Request $request)
    {
        $level = $request->get('level', 'all');

        $query = DocumentVerification::with([
                'documentRequest.documentType',
                'documentRequest.user',
                'authority'
            ])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc');

        if ($level !== 'all' && in_array($level, [1, 2, 3])) {
            $query->where('verification_level', $level);
        }

        $verifications = $query->paginate(20);

        $stats = [
            'total_pending' => $verifications->total(),
            'today' => DocumentVerification::where('status', 'pending')
                ->whereDate('created_at', today())
                ->count(),
            'this_week' => DocumentVerification::where('status', 'pending')
                ->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])
                ->count(),
            'by_level' => [
                'level_1' => DocumentVerification::where('status', 'pending')->where('verification_level', 1)->count(),
                'level_2' => DocumentVerification::where('status', 'pending')->where('verification_level', 2)->count(),
                'level_3' => DocumentVerification::where('status', 'pending')->where('verification_level', 3)->count(),
            ],
        ];

        return view('admin.verifications.pending', compact('verifications', 'stats', 'level'));
    }

    public function approved(Request $request)
    {
        $level = $request->get('level', 'all');
        $query = DocumentVerification::with([
                'documentRequest.documentType',
                'documentRequest.user',
                'authority'
            ])
            ->where('status', 'approved')
            ->orderBy('updated_at', 'desc');

        if ($level !== 'all' && in_array($level, [1, 2, 3])) {
            $query->where('verification_level', $level);
        }

        $verifications = $query->paginate(20);

        $stats = [
            'total_approved' => $verifications->total(),
            'today' => DocumentVerification::where('status', 'approved')
                ->whereDate('updated_at', today())
                ->count(),
            'this_week' => DocumentVerification::where('status', 'approved')
                ->whereBetween('updated_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])
                ->count(),
            'by_level' => [
                'level_1' => DocumentVerification::where('status', 'approved')->where('verification_level', 1)->count(),
                'level_2' => DocumentVerification::where('status', 'approved')->where('verification_level', 2)->count(),
                'level_3' => DocumentVerification::where('status', 'approved')->where('verification_level', 3)->count(),
            ],
        ];

        return view('admin.verifications.approved', compact('verifications', 'stats', 'level'));
    }

    public function rejected(Request $request)
    {
        $level = $request->get('level', 'all');

        $query = DocumentVerification::with([
                'documentRequest.documentType',
                'documentRequest.user',
                'authority'
            ])
            ->where('status', 'rejected')
            ->orderBy('updated_at', 'desc');

        if ($level !== 'all' && in_array($level, [1, 2, 3])) {
            $query->where('verification_level', $level);
        }

        $verifications = $query->paginate(20);

        $stats = [
            'total_rejected' => $verifications->total(),
            'today' => DocumentVerification::where('status', 'rejected')
                ->whereDate('updated_at', today())
                ->count(),
            'this_week' => DocumentVerification::where('status', 'rejected')
                ->whereBetween('updated_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])
                ->count(),
            'by_level' => [
                'level_1' => DocumentVerification::where('status', 'rejected')->where('verification_level', 1)->count(),
                'level_2' => DocumentVerification::where('status', 'rejected')->where('verification_level', 2)->count(),
                'level_3' => DocumentVerification::where('status', 'rejected')->where('verification_level', 3)->count(),
            ],
        ];

        return view('admin.verifications.rejected', compact('verifications', 'stats', 'level'));
    }

    public function showSendForm($documentId)
    {
        $document = DocumentRequest::with('documentType')->findOrFail($documentId);

        if ($document->status->value !== 'approved') {
            return redirect()
                ->back()
                ->with('error', 'Dokumen harus dalam status approved untuk diverifikasi.');
        }

        if ($document->verifications()->whereIn('status', ['pending'])->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Dokumen sudah memiliki request verifikasi yang sedang berjalan.');
        }

        $ketuaAkademik = SignatureAuthority::where('authority_type', 'ketua_akademik')
            ->where('is_active', true)
            ->first();

        if (!$ketuaAkademik) {
            return redirect()
                ->back()
                ->with('error', 'Pejabat Ketua Akademik belum dikonfigurasi. Hubungi admin sistem.');
        }

        return view('admin.documents.send-verification', compact('document', 'ketuaAkademik'));
    }

    public function sendVerification(Request $request, $documentId)
    {
        try {
            DB::beginTransaction();

            $document = DocumentRequest::findOrFail($documentId);

            if ($document->status->value !== 'approved') {
                return redirect()->back()->with('error', 'Dokumen harus dalam status approved untuk diverifikasi.');
            }

            if ($document->verifications()->where('status', 'pending')->exists()) {
                return redirect()->back()->with('error', 'Dokumen sudah memiliki request verifikasi yang sedang berjalan.');
            }

            $ketuaAkademik = SignatureAuthority::where('authority_type', 'ketua_akademik')
                ->where('is_active', true)
                ->first();

            if (!$ketuaAkademik) {
                return redirect()->back()->with('error', 'Pejabat Ketua Akademik tidak ditemukan.');
            }

            $verification = $this->verificationService->sendToLevel($document, 1);

            DB::commit();

            return redirect()
                ->route('admin.documents.show', $document->id)
                ->with('success', '🎉 Request verifikasi 3-level berhasil dimulai! Level 1 (Ketua Akademik) telah dikirim via WhatsApp.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Gagal mengirim request verifikasi: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function resendVerification($documentId)
    {
        try {
            $document = DocumentRequest::findOrFail($documentId);

            $verification = $document->verifications()
                ->where('verification_level', $document->current_verification_step)
                ->where('status', 'pending')
                ->first();

            if (!$verification) {
                return redirect()->back()->with('error', 'Tidak ada verifikasi pending yang bisa dikirim ulang.');
            }

            $this->verificationService->resendVerificationRequest($verification);

            return redirect()
                ->back()
                ->with('success', 'Request verifikasi berhasil dikirim ulang via WhatsApp! 📱');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal mengirim ulang: ' . $e->getMessage());
        }
    }

    public function cancelVerification($documentId)
    {
        try {
            DB::beginTransaction();

            $document = DocumentRequest::findOrFail($documentId);

            $verification = $document->verifications()
                ->where('verification_level', $document->current_verification_step)
                ->where('status', 'pending')
                ->first();

            if (!$verification) {
                return redirect()->back()->with('error', 'Tidak ada verifikasi pending yang bisa dibatalkan.');
            }

            $this->verificationService->cancelVerification($verification);

            DB::commit();

            return redirect()
                ->route('admin.documents.show', $document->id)
                ->with('success', 'Request verifikasi berhasil dibatalkan.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Gagal membatalkan verifikasi: ' . $e->getMessage());
        }
    }

    public function show($verificationId)
    {
        $verification = DocumentVerification::with([
            'documentRequest.documentType',
            'documentRequest.user',
            'documentRequest.verifications',
            'authority'
        ])->findOrFail($verificationId);

        return view('admin.verifications.show', compact('verification'));
    }

    public function stats()
    {
        $stats = [
            'total' => DocumentVerification::count(),
            'pending' => DocumentVerification::where('status', 'pending')->count(),
            'approved' => DocumentVerification::where('status', 'approved')->count(),
            'rejected' => DocumentVerification::where('status', 'rejected')->count(),
            'by_level' => [
                'level_1' => [
                    'total' => DocumentVerification::where('verification_level', 1)->count(),
                    'pending' => DocumentVerification::where('verification_level', 1)->where('status', 'pending')->count(),
                    'approved' => DocumentVerification::where('verification_level', 1)->where('status', 'approved')->count(),
                    'rejected' => DocumentVerification::where('verification_level', 1)->where('status', 'rejected')->count(),
                ],
                'level_2' => [
                    'total' => DocumentVerification::where('verification_level', 2)->count(),
                    'pending' => DocumentVerification::where('verification_level', 2)->where('status', 'pending')->count(),
                    'approved' => DocumentVerification::where('verification_level', 2)->where('status', 'approved')->count(),
                    'rejected' => DocumentVerification::where('verification_level', 2)->where('status', 'rejected')->count(),
                ],
                'level_3' => [
                    'total' => DocumentVerification::where('verification_level', 3)->count(),
                    'pending' => DocumentVerification::where('verification_level', 3)->where('status', 'pending')->count(),
                    'approved' => DocumentVerification::where('verification_level', 3)->where('status', 'approved')->count(),
                    'rejected' => DocumentVerification::where('verification_level', 3)->where('status', 'rejected')->count(),
                ],
            ],
            'today' => [
                'total' => DocumentVerification::whereDate('created_at', today())->count(),
                'pending' => DocumentVerification::where('status', 'pending')
                    ->whereDate('created_at', today())
                    ->count(),
                'approved' => DocumentVerification::where('status', 'approved')
                    ->whereDate('updated_at', today())
                    ->count(),
                'rejected' => DocumentVerification::where('status', 'rejected')
                    ->whereDate('updated_at', today())
                    ->count(),
            ],
            'this_week' => [
                'total' => DocumentVerification::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'approved' => DocumentVerification::where('status', 'approved')
                    ->whereBetween('updated_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])->count(),
                'rejected' => DocumentVerification::where('status', 'rejected')
                    ->whereBetween('updated_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
