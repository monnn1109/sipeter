<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignatureVerificationRequest;
use App\Models\{DocumentSignature, DocumentRequest};
use App\Services\SignatureService;
use App\Enums\{SignatureStatus, DocumentStatus};
use App\Events\{SignatureVerified, SignatureRejected};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Storage, Log};

class SignatureVerificationController extends Controller
{
    protected SignatureService $signatureService;

    public function __construct(SignatureService $signatureService)
    {
        $this->signatureService = $signatureService;
    }

    /**
     * ✅ FIXED: List signatures untuk verifikasi
     */
    public function index(Request $request)
    {
        $query = DocumentSignature::with([
            'documentRequest.documentType',
            'documentRequest.user',
            'signatureAuthority'
        ])->uploaded();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('documentRequest', function ($q) use ($search) {
                $q->where('request_code', 'like', "%{$search}%")
                  ->orWhere('applicant_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('authority')) {
            $query->where('signature_authority_id', $request->authority);
        }

        $signatures = $query->latest('uploaded_at')->paginate(20);

        $uploadedCount = DocumentSignature::uploaded()->count();
        $todayCount = DocumentSignature::uploaded()
            ->whereDate('uploaded_at', today())
            ->count();
        $weekCount = DocumentSignature::uploaded()
            ->whereBetween('uploaded_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->count();

        $authorities = \App\Models\SignatureAuthority::active()->orderBy('name')->get();

        // ✅ FIXED: Kirim flag 'showList' = true
        return view('admin.signatures.verify', compact(
            'signatures',
            'uploadedCount',
            'todayCount',
            'weekCount',
            'authorities'
        ))->with('showList', true);
    }

    /**
     * ✅ FIXED: Form detail verifikasi 1 signature
     */
    public function verifyForm($id)
    {
        $signature = DocumentSignature::with([
            'documentRequest.documentType',
            'documentRequest.user',
            'signatureAuthority',
            'verifiedBy'
        ])->findOrFail($id);

        $previousSignatures = DocumentSignature::with('documentRequest')
            ->where('signature_authority_id', $signature->signature_authority_id)
            ->where('status', SignatureStatus::VERIFIED->value)
            ->where('id', '!=', $id)
            ->latest('verified_at')
            ->take(4)
            ->get();

        // ✅ FIXED: Kirim $signature + flag 'showList' = false
        return view('admin.signatures.verify', compact('signature', 'previousSignatures'))
            ->with('showList', false);
    }

    /**
     * 🆕 Preview Signature (Called from detail.blade.php JavaScript)
     *
     * Opens signature image directly in new tab
     * Route: GET /admin/signatures/{id}/preview
     */
    public function preview($id)
    {
        try {
            $signature = DocumentSignature::findOrFail($id);

            // Check if signature file exists
            if (!$signature->signature_file) {
                abort(404, 'File TTD tidak ditemukan');
            }

            if (!Storage::exists($signature->signature_file)) {
                Log::error('Signature file not found in storage', [
                    'signature_id' => $id,
                    'file_path' => $signature->signature_file
                ]);
                abort(404, 'File TTD tidak ditemukan di storage');
            }

            // Get file path and mime type
            $filePath = Storage::path($signature->signature_file);
            $mimeType = Storage::mimeType($signature->signature_file);

            // Log preview access
            Log::info('Signature file previewed', [
                'signature_id' => $id,
                'admin_id' => Auth::id(),
                'admin_name' => Auth::user()->name ?? 'Unknown'
            ]);

            // Return image file directly for browser display
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="TTD_Level_' . $signature->signature_level . '.png"'
            ]);

        } catch (\Exception $e) {
            Log::error('Signature preview failed', [
                'signature_id' => $id,
                'error' => $e->getMessage()
            ]);

            abort(404, 'Signature tidak ditemukan');
        }
    }

    /**
     * ✅ FIXED: Approve signature (3-Level Sequential Logic + UPDATE current_signature_step)
     *
     * Route: POST /admin/signatures/{id}/approve
     * Called from: detail.blade.php - Verify Signature Modal
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $signature = DocumentSignature::with([
                'documentRequest',
                'signatureAuthority'
            ])->findOrFail($id);

            // ✅ CHECK: Status harus UPLOADED
            if ($signature->status !== SignatureStatus::UPLOADED) {
                DB::rollBack();

                Log::warning('Signature approve - Invalid status', [
                    'signature_id' => $id,
                    'current_status' => $signature->status->value,
                    'expected_status' => 'uploaded'
                ]);

                return back()->with('error', '⚠️ Signature sudah diverifikasi atau tidak dalam status uploaded!');
            }

            $document = $signature->documentRequest;
            $level = $signature->signature_level;

            // ✅ STEP 1: Mark signature as VERIFIED
            $signature->markAsVerified(Auth::user(), $request->notes);

            Log::info('Signature verified', [
                'signature_id' => $id,
                'level' => $level,
                'admin_id' => Auth::id(),
            ]);

            // ✅ STEP 2: Update document current_signature_step
            $document->update([
                'current_signature_step' => $level, // ✅ CRITICAL: Update to current level
            ]);

            Log::info('Document current_signature_step updated', [
                'document_id' => $document->id,
                'current_signature_step' => $level,
            ]);

            // ✅ STEP 3: Fire event untuk notifikasi
            event(new SignatureVerified($signature, $document, $signature->signatureAuthority, Auth::user()));

            // ✅ STEP 4: Check if this is final level (Level 3)
            if ($level >= 3) {
                $document->update([
                    'status' => DocumentStatus::SIGNATURE_VERIFIED,
                ]);

                DB::commit();

                Log::info('All signature levels completed', [
                    'document_id' => $document->id,
                ]);

                return back()->with('success', '🎉 Semua TTD Level 3 selesai! Dokumen siap untuk finalisasi. Silakan embed TTD ke PDF dan upload dokumen final.');
            }

            // ✅ STEP 5: Auto-trigger next level via Service
            $result = $this->signatureService->proceedToNextLevel($document, $level);

            DB::commit();

            if (!$result['success']) {
                // Rollback jika gagal proceed
                return back()->with('warning', '✅ Level ' . $level . ' verified, tapi gagal proceed ke level berikutnya: ' . $result['message']);
            }

            Log::info('Signature verified and proceeded to next level', [
                'signature_id' => $id,
                'level_verified' => $level,
                'next_level' => $level + 1,
            ]);

            return back()->with('success', '✅ TTD Level ' . $level . ' terverifikasi! Otomatis lanjut ke Level ' . ($level + 1) . '. Notifikasi WA sudah dikirim.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Signature approve failed', [
                'signature_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', '❌ Gagal memproses verifikasi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * ✅ Reject signature
     *
     * Route: POST /admin/signatures/{id}/reject
     * Called from: detail.blade.php - Reject Signature Modal
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $signature = DocumentSignature::with([
                'documentRequest',
                'signatureAuthority'
            ])->findOrFail($id);

            // ✅ CHECK: Status harus UPLOADED
            if ($signature->status !== SignatureStatus::UPLOADED) {
                DB::rollBack();

                Log::warning('Signature reject - Invalid status', [
                    'signature_id' => $id,
                    'current_status' => $signature->status->value
                ]);

                return back()->with('error', '⚠️ Signature sudah diverifikasi atau tidak dalam status uploaded!');
            }

            // ✅ USE SIGNATURE SERVICE
            $result = $this->signatureService->rejectSignature(
                $signature,
                Auth::user(),
                $request->reason
            );

            DB::commit();

            if (!$result['success']) {
                return back()->with('error', $result['message']);
            }

            Log::info('Signature rejected', [
                'signature_id' => $id,
                'level' => $signature->signature_level,
                'reason' => $request->reason,
                'admin_id' => Auth::id()
            ]);

            return back()->with('warning', '⚠️ ' . $result['message']);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Signature reject failed', [
                'signature_id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()
                ->with('error', '❌ Gagal memproses penolakan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download signature file
     */
    public function downloadSignature($id)
    {
        try {
            $signature = DocumentSignature::with('documentRequest')->findOrFail($id);

            if (!$signature->signature_file) {
                abort(404, 'File tidak ditemukan');
            }

            if (!Storage::exists($signature->signature_file)) {
                abort(404, 'File tidak ditemukan di storage');
            }

            Log::info('Signature file downloaded', [
                'signature_id' => $id,
                'document_code' => $signature->documentRequest->request_code,
                'admin_id' => Auth::id()
            ]);

            return Storage::download(
                $signature->signature_file,
                'TTD_Level_' . $signature->signature_level . '_' . $signature->documentRequest->request_code . '.png'
            );

        } catch (\Exception $e) {
            Log::error('Signature download failed', [
                'signature_id' => $id,
                'error' => $e->getMessage()
            ]);

            abort(500, 'Gagal mengunduh file');
        }
    }

    /**
     * 🆕 Get signature data (AJAX)
     * Returns JSON for preview modal
     */
    public function getSignatureData($id)
    {
        try {
            $signature = DocumentSignature::with([
                'documentRequest.documentType',
                'signatureAuthority',
                'verifiedBy'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $signature->id,
                    'document_code' => $signature->documentRequest->request_code,
                    'document_type' => $signature->documentRequest->documentType->name,
                    'signature_level' => $signature->signature_level,
                    'authority_name' => $signature->signatureAuthority->name,
                    'authority_position' => $signature->signatureAuthority->position,
                    'authority_type' => $signature->signatureAuthority->getAuthorityTypeLabel(),
                    'status' => $signature->status->value,
                    'status_label' => $signature->status->label(),
                    'signature_url' => $signature->signature_file ? Storage::url($signature->signature_file) : null,
                    'qr_code_url' => $signature->qr_code_file ? Storage::url($signature->qr_code_file) : null,
                    'uploaded_at' => $signature->uploaded_at?->format('d/m/Y H:i'),
                    'verified_at' => $signature->verified_at?->format('d/m/Y H:i'),
                    'verified_by' => $signature->verifiedBy?->name,
                    'verification_notes' => $signature->verification_notes,
                    'rejection_reason' => $signature->rejection_reason,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Signature not found'
            ], 404);
        }
    }
}
