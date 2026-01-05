<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{DocumentSignature, DocumentRequest, SignatureAuthority};
use App\Enums\{DocumentStatus, SignatureStatus};
use App\Events\SignatureRequested;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Storage, Log};
use Illuminate\Support\Str;

class SignatureManagementController extends Controller
{
    public function index(Request $request)
    {
        $stats = $this->getSignatureStats();

        $status = $request->get('status', 'all');
        $query = DocumentSignature::with([
            'documentRequest.documentType',
            'documentRequest.user',
            'signatureAuthority'
        ]);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $signatures = $query->latest()->paginate(15);

        $authorities = SignatureAuthority::active()->get();

        return view('admin.signatures.index', compact('stats', 'signatures', 'authorities', 'status'));
    }

    public function pending(Request $request)
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

        if ($request->filled('date_from')) {
            $query->whereDate('uploaded_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('uploaded_at', '<=', $request->date_to);
        }

        $signatures = $query->latest('uploaded_at')->paginate(20);

        $uploadedCount = DocumentSignature::uploaded()->count();
        $requestedCount = DocumentSignature::requested()->count();
        $overdueCount = DocumentSignature::uploaded()
            ->where('uploaded_at', '<', now()->subDays(3))
            ->count();

        $weekCount = DocumentSignature::uploaded()
            ->whereBetween('uploaded_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->count();

        $authorities = SignatureAuthority::active()->orderBy('name')->get();

        return view('admin.signatures.pending', compact(
            'signatures',
            'uploadedCount',
            'requestedCount',
            'overdueCount',
            'weekCount',
            'authorities'
        ));
    }

    public function history(Request $request)
    {
        $query = DocumentSignature::with([
            'documentRequest.documentType',
            'documentRequest.user',
            'signatureAuthority',
            'verifiedBy',
            'rejectedBy'
        ])
        ->whereIn('status', [
            SignatureStatus::VERIFIED->value,
            SignatureStatus::REJECTED->value
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('documentRequest', function ($q) use ($search) {
                $q->where('request_code', 'like', "%{$search}%")
                  ->orWhere('applicant_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('authority')) {
            $query->where('signature_authority_id', $request->authority);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $signatures = $query->latest('updated_at')->paginate(20);

        $totalCount = DocumentSignature::whereIn('status', [
            SignatureStatus::VERIFIED->value,
            SignatureStatus::REJECTED->value
        ])->count();

        $verifiedCount = DocumentSignature::verified()->count();
        $rejectedCount = DocumentSignature::rejected()->count();

        $monthCount = DocumentSignature::whereIn('status', [
            SignatureStatus::VERIFIED->value,
            SignatureStatus::REJECTED->value
        ])
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

        $authorities = SignatureAuthority::active()->orderBy('name')->get();

        return view('admin.signatures.history', compact(
            'signatures',
            'totalCount',
            'verifiedCount',
            'rejectedCount',
            'monthCount',
            'authorities'
        ));
    }

    /**
     * ✅ FIXED: Request TTD - Start from Level 1 ONLY if current_signature_step = 0
     *
     * Logic Flow:
     * 1. Check all verifications completed (Level 1, 2, 3 approved)
     * 2. Check current_signature_step
     *    - If 0 → Request Level 1 (Pa Riko)
     *    - If > 0 → Error (sudah ada request, handled by Service auto-trigger)
     */
    public function requestSignature(Request $request, $documentId)
    {
        try {
            DB::beginTransaction();

            $document = DocumentRequest::with([
                'documentType',
                'verifications',
                'signatures'
            ])->findOrFail($documentId);

            Log::info('Request TTD - Document loaded', [
                'document_id' => $documentId,
                'document_code' => $document->request_code,
                'current_status' => $document->status->value,
                'current_signature_step' => $document->current_signature_step ?? 0,
            ]);

            // ✅ CHECK 1: All verifications must be approved (Level 1, 2, 3)
            $allVerificationsApproved = $document->verifications()
                ->whereIn('verification_level', [1, 2, 3])
                ->where('decision', 'approved')
                ->count() === 3;

            if (!$allVerificationsApproved) {
                DB::rollBack();

                $approvedLevels = $document->verifications()
                    ->where('decision', 'approved')
                    ->pluck('verification_level')
                    ->toArray();

                Log::warning('Request TTD - Not all verifications approved', [
                    'approved_levels' => $approvedLevels,
                    'required_levels' => [1, 2, 3],
                ]);

                return back()->with('error', '❌ Dokumen belum melewati semua verifikasi! Hanya Level ' . implode(', ', $approvedLevels) . ' yang sudah disetujui.');
            }

            // ✅ CHECK 2: current_signature_step harus 0 (belum ada request TTD)
            $currentSignatureStep = $document->current_signature_step ?? 0;

            if ($currentSignatureStep > 0) {
                DB::rollBack();

                Log::warning('Request TTD - Already in progress', [
                    'current_signature_step' => $currentSignatureStep,
                ]);

                return back()->with('error', '⚠️ Request TTD sudah dimulai (Step ' . $currentSignatureStep . '). Proses otomatis akan lanjut setelah verifikasi.');
            }

            // ✅ CHECK 3: Apakah sudah ada signature Level 1 yang active?
            $existingLevel1 = DocumentSignature::where('document_request_id', $document->id)
                ->where('signature_level', 1)
                ->whereIn('status', [SignatureStatus::REQUESTED, SignatureStatus::UPLOADED])
                ->first();

            if ($existingLevel1) {
                DB::rollBack();

                Log::warning('Request TTD Level 1 - Already exists', [
                    'signature_id' => $existingLevel1->id,
                    'signature_status' => $existingLevel1->status->value,
                ]);

                return back()->with('error', '⚠️ Request TTD Level 1 sudah ada! Status: ' . $existingLevel1->status->label());
            }

            // ✅ GET: Ketua Akademik (Level 1)
            $authority = SignatureAuthority::getActiveKetuaAkademik();

            if (!$authority || !$authority->is_active) {
                DB::rollBack();

                Log::warning('Request TTD - Ketua Akademik not found or inactive');

                return back()->with('error', '❌ Pejabat Ketua Akademik tidak ditemukan atau tidak aktif!');
            }

            Log::info('Request TTD - Authority found', [
                'authority_id' => $authority->id,
                'authority_name' => $authority->name,
                'authority_type' => $authority->authority_type->value,
                'level' => 1,
            ]);

            // ✅ CREATE: Signature request Level 1
            $token = Str::random(64);
            $tokenExpiry = now()->addDays(7);

            $signature = DocumentSignature::create([
                'document_request_id' => $document->id,
                'signature_authority_id' => $authority->id,
                'signature_level' => 1,
                'status' => SignatureStatus::REQUESTED,
                'requested_at' => now(),
                'metadata' => [
                    'token' => $token,
                    'token_expires_at' => $tokenExpiry->toDateTimeString(),
                    'requested_by' => Auth::id(),
                    'admin_notes' => $request->notes ?? null,
                ]
            ]);

            Log::info('Request TTD - Signature Level 1 created', [
                'signature_id' => $signature->id,
                'signature_level' => 1,
                'token_preview' => substr($token, 0, 10) . '...',
                'expires_at' => $tokenExpiry->toDateTimeString(),
            ]);

            // ✅ UPDATE: Document status & current_signature_step
            $document->update([
                'status' => DocumentStatus::SIGNATURE_REQUESTED,
                'current_signature_step' => 1, // ✅ CRITICAL: Set to 1
                'signature_requested_at' => now()
            ]);

            Log::info('Request TTD - Document updated', [
                'new_status' => DocumentStatus::SIGNATURE_REQUESTED->value,
                'current_signature_step' => 1,
            ]);

            // ✅ FIRE: Event untuk kirim WA ke Pa Riko
            $uploadLink = route('signature.upload.show', $token);

            event(new SignatureRequested($signature, $document, $authority, $uploadLink));

            Log::info('Request TTD - Event fired successfully for Level 1', [
                'upload_link' => $uploadLink,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.documents.show', $document->id)
                ->with('success', '✅ Request tanda tangan Level 1 berhasil dikirim ke ' . $authority->name . '!')
                ->with('upload_link', $uploadLink);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Request TTD - Exception occurred', [
                'document_id' => $documentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', '❌ Error: ' . $e->getMessage());
        }
    }

    public function resendSignatureRequest($id)
    {
        try {
            DB::beginTransaction();

            $signature = DocumentSignature::with([
                'documentRequest',
                'signatureAuthority'
            ])->findOrFail($id);

            if ($signature->status !== SignatureStatus::REQUESTED) {
                DB::rollBack();
                return back()->with('error', 'Signature sudah diupload atau tidak dalam status requested!');
            }

            $newToken = Str::random(64);
            $tokenExpiry = now()->addDays(7);

            $signature->update([
                'metadata' => array_merge($signature->metadata ?? [], [
                    'token' => $newToken,
                    'token_expires_at' => $tokenExpiry->toDateTimeString(),
                    'resent_at' => now()->toDateTimeString(),
                    'resent_by' => Auth::id(),
                ])
            ]);

            $uploadLink = route('signature.upload.show', $newToken);

            event(new SignatureRequested(
                $signature,
                $signature->documentRequest,
                $signature->signatureAuthority,
                $uploadLink
            ));

            DB::commit();

            return back()
                ->with('success', '✅ Request tanda tangan berhasil dikirim ulang!')
                ->with('upload_link', $uploadLink);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengirim ulang: ' . $e->getMessage());
        }
    }

    public function sendReminder($documentId)
    {
        try {
            $document = DocumentRequest::findOrFail($documentId);

            $signature = $document->signatures()
                ->where('verification_level', $document->current_signature_step)
                ->where('status', 'requested')
                ->first();

            if (!$signature) {
                return back()->with('error', '❌ Tidak ada signature pending yang bisa dikirim reminder!');
            }

            $token = $signature->metadata['token'] ?? null;

            if (!$token) {
                return back()->with('error', '❌ Token tidak ditemukan!');
            }

            $uploadLink = route('signature.upload.show', $token);

            event(new SignatureRequested(
                $signature,
                $signature->documentRequest,
                $signature->signatureAuthority,
                $uploadLink
            ));

            return back()->with('success', '✅ Reminder berhasil dikirim!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim reminder: ' . $e->getMessage());
        }
    }

    public function viewSignature($id)
    {
        $signature = DocumentSignature::with([
            'documentRequest.documentType',
            'documentRequest.user',
            'signatureAuthority',
            'verifiedBy',
            'rejectedBy'
        ])->findOrFail($id);

        return view('admin.signatures.index', compact('signature'));
    }

    public function details($id)
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
                    'authority_name' => $signature->signatureAuthority->name,
                    'authority_position' => $signature->signatureAuthority->position,
                    'status' => $signature->status->value,
                    'status_label' => $signature->status->getLabel(),
                    'requested_at' => $signature->requested_at?->format('d/m/Y H:i'),
                    'uploaded_at' => $signature->uploaded_at?->format('d/m/Y H:i'),
                    'verified_at' => $signature->verified_at?->format('d/m/Y H:i'),
                    'signature_url' => $signature->signature_file ? Storage::url($signature->signature_file) : null,
                    'qr_code_url' => $signature->qr_code_file ? Storage::url($signature->qr_code_file) : null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Signature not found'
            ], 404);
        }
    }

    public function authorities(Request $request)
    {
        $query = SignatureAuthority::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('position', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('authority_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $authorities = $query->withCount('signatures')->latest()->paginate(20);

        $activeCount = SignatureAuthority::active()->count();
        $inactiveCount = SignatureAuthority::inactive()->count();

        $monthSignatureCount = DocumentSignature::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('admin.signatures.authorities', compact(
            'authorities',
            'activeCount',
            'inactiveCount',
            'monthSignatureCount'
        ));
    }

    public function createAuthority()
    {
        return view('admin.signatures.authorities-create');
    }

    public function storeAuthority(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'authority_type' => 'required|in:academic,student_affairs,rector,vice_rector',
            'email' => 'required|email|unique:signature_authorities,email',
            'whatsapp_number' => 'required|string|max:20',
            'nip' => 'nullable|string|max:50',
            'is_active' => 'boolean'
        ]);

        try {
            SignatureAuthority::create($request->all());

            return redirect()
                ->route('admin.signatures.authorities')
                ->with('success', '✅ Pejabat berhasil ditambahkan!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal menambahkan pejabat: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function editAuthority($id)
    {
        $authority = SignatureAuthority::findOrFail($id);
        return view('admin.signatures.authorities-edit', compact('authority'));
    }

    public function updateAuthority(Request $request, $id)
    {
        $authority = SignatureAuthority::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'authority_type' => 'required|in:academic,student_affairs,rector,vice_rector',
            'email' => 'required|email|unique:signature_authorities,email,' . $id,
            'whatsapp_number' => 'required|string|max:20',
            'nip' => 'nullable|string|max:50',
            'is_active' => 'boolean'
        ]);

        try {
            $authority->update($request->all());

            return redirect()
                ->route('admin.signatures.authorities')
                ->with('success', '✅ Data pejabat berhasil diupdate!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal mengupdate: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function toggleStatus($id)
    {
        try {
            $authority = SignatureAuthority::findOrFail($id);
            $authority->update(['is_active' => !$authority->is_active]);

            $status = $authority->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return back()->with('success', "✅ Pejabat berhasil {$status}!");

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    public function deleteAuthority($id)
    {
        try {
            $authority = SignatureAuthority::findOrFail($id);

            if ($authority->signatures()->exists()) {
                return back()->with('error', '❌ Tidak dapat menghapus pejabat yang sudah memiliki signature!');
            }

            $authority->delete();

            return back()->with('success', '✅ Pejabat berhasil dihapus!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function exportHistory(Request $request)
    {
    }

    public function exportHistoryPdf(Request $request)
    {
    }

    public function getStats()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getSignatureStats()
        ]);
    }

    private function getSignatureStats()
    {
        $total = DocumentSignature::count();
        $requested = DocumentSignature::requested()->count();
        $uploaded = DocumentSignature::uploaded()->count();
        $verified = DocumentSignature::verified()->count();
        $rejected = DocumentSignature::rejected()->count();
        $pending = $requested + $uploaded;

        return [
            'total' => $total,
            'requested' => $requested,
            'uploaded' => $uploaded,
            'verified' => $verified,
            'rejected' => $rejected,
            'pending' => $pending,
            'overdue' => DocumentSignature::requested()
                ->where('requested_at', '<', now()->subHours(24))
                ->count(),
            'today_uploaded' => DocumentSignature::uploaded()
                ->whereDate('uploaded_at', today())
                ->count(),
            'week_verified' => DocumentSignature::verified()
                ->whereBetween('verified_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])
                ->count(),
            'month_verified' => DocumentSignature::verified()
                ->whereMonth('verified_at', now()->month)
                ->count(),
        ];
    }
}
