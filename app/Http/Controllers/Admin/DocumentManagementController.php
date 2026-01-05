<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Services\{
    DocumentHistoryService,
    NotificationService,
    DocumentUploadService
};
use App\Events\{
    DocumentRequestApproved,
    DocumentRequestRejected,
    DocumentReadyForPickup
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Storage, DB, Log};

class DocumentManagementController extends Controller
{
    public function __construct(
        private DocumentHistoryService $historyService,
        private NotificationService $notificationService,
        private DocumentUploadService $uploadService
    ) {}

    public function index(Request $request)
    {
        $query = DocumentRequest::with([
            'documentType',
            'uploadedBy',
            'approvedBy',
            'user',
            'verifications',
            'verifications.authority',
            'signatures',
            'signatures.authority'
        ]);

        $this->applyFilters($query, $request);

        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $documents = $query->paginate(20)->withQueryString();

        return view('admin.documents.index', [
            'documents' => $documents,
            'stats' => $this->getStatistics(),
            'title' => 'Manajemen Dokumen',
            'active' => 'documents'
        ]);
    }

    public function pending()
    {
        $documents = DocumentRequest::where('status', 'pending')
            ->with(['documentType', 'user'])
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return view('admin.documents.pending', [
            'documents' => $documents,
            'title' => 'Dokumen Pending',
            'active' => 'documents-pending'
        ]);
    }

    public function show($id)
    {
        $documentRequest = DocumentRequest::with([
            'documentType',
            'activities.user',
            'approvedBy',
            'uploadedBy',
            'user',
            'verifications',
            'verifications.authority',
            'signatures',
            'signatures.authority'
        ])->findOrFail($id);

        $verificationProgress = [
            'current_step' => $documentRequest->current_verification_step ?? 0,
            'level_1' => $documentRequest->verifications->where('verification_level', 1)->first(),
            'level_2' => $documentRequest->verifications->where('verification_level', 2)->first(),
            'level_3' => $documentRequest->verifications->where('verification_level', 3)->first(),
            'is_all_completed' => $documentRequest->current_verification_step === 3
                && $documentRequest->verifications->where('verification_level', 3)->where('status', 'approved')->isNotEmpty(),
        ];

        return view('admin.documents.detail', [
            'documentRequest' => $documentRequest,
            'verificationProgress' => $verificationProgress,
            'title' => 'Detail Dokumen - ' . $documentRequest->request_code,
            'active' => 'documents'
        ]);
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500'
        ]);

        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            if (!in_array($documentRequest->status->value, ['submitted', 'pending'])) {
                return back()->with('error', 'Dokumen sudah diproses sebelumnya');
            }

            DB::beginTransaction();

            $documentRequest->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'admin_notes' => $request->admin_notes,
            ]);

            event(new DocumentRequestApproved($documentRequest, false));

            $this->historyService->logStatusChange(
                $documentRequest,
                'pending',
                'approved',
                'Dokumen disetujui oleh admin'
            );

            DB::commit();

            return back()->with('success', 'Pengajuan berhasil disetujui! Silakan lanjutkan dengan Request Verifikasi 3 Level.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approve document failed', [
                'document_id' => $id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Gagal menyetujui dokumen: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            if ($documentRequest->status->value !== 'pending') {
                return back()->with('error', 'Dokumen sudah diproses sebelumnya');
            }

            DB::beginTransaction();

            $documentRequest->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'approved_by' => auth()->id(),
            ]);

            event(new DocumentRequestRejected($documentRequest));

            DB::commit();

            return back()->with('success', 'Pengajuan berhasil ditolak.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reject document failed', [
                'document_id' => $id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Gagal menolak dokumen: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:processing,ready_for_pickup,picked_up,completed',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $documentRequest = DocumentRequest::findOrFail($id);
            $oldStatus = $documentRequest->status->value;
            $newStatus = $request->status;

            DB::beginTransaction();

            $updateData = ['status' => $newStatus];

            switch ($newStatus) {
                case 'ready_for_pickup':
                    $updateData['ready_at'] = now();
                    break;
                case 'picked_up':
                    $updateData['picked_up_at'] = now();
                    break;
                case 'completed':
                    $updateData['completed_at'] = now();
                    break;
            }

            $documentRequest->update($updateData);

            if ($newStatus === 'ready_for_pickup') {
                event(new DocumentReadyForPickup($documentRequest));
            }

            $this->historyService->logStatusChange($documentRequest, $oldStatus, $newStatus, $request->notes);

            DB::commit();

            $statusLabels = [
                'ready_for_pickup' => 'Siap Diambil',
                'picked_up' => 'Sudah Diambil',
                'completed' => 'Selesai'
            ];

            $message = 'Status berhasil diubah menjadi: ' . ($statusLabels[$newStatus] ?? $newStatus);

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update status failed', [
                'document_id' => $id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    public function addNote(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|max:1000'
        ]);

        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            $currentNotes = $documentRequest->admin_notes ?? '';
            $newNote = "\n\n[" . now()->format('d/m/Y H:i') . " - " . auth()->user()->name . "]\n" . $request->notes;

            $documentRequest->update([
                'admin_notes' => $currentNotes . $newNote
            ]);

            $documentRequest->activities()->create([
                'user_id' => auth()->id(),
                'actor_name' => auth()->user()->name,
                'actor_type' => 'admin',
                'activity_type' => 'note_added',
                'status_from' => $documentRequest->status->value,
                'status_to' => $documentRequest->status->value,
                'description' => 'Admin menambahkan catatan: ' . $request->notes,
            ]);

            return back()->with('success', 'Catatan berhasil ditambahkan');

        } catch (\Exception $e) {
            Log::error('Add note failed', [
                'document_id' => $id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Gagal menambahkan catatan: ' . $e->getMessage());
        }
    }

    public function download($id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            if (!$documentRequest->hasFile()) {
                abort(404, 'File tidak ditemukan');
            }

            $filePath = $documentRequest->file_path;

            if (!Storage::disk('documents')->exists($filePath)) {
                abort(404, 'File tidak ditemukan di storage');
            }

            Log::info('Admin downloaded document file', [
                'document_id' => $documentRequest->id,
                'request_code' => $documentRequest->request_code,
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name
            ]);

            $documentRequest->activities()->create([
                'user_id' => auth()->id(),
                'actor_name' => auth()->user()->name,
                'actor_type' => 'admin',
                'activity_type' => 'downloaded',
                'status_from' => $documentRequest->status->value,
                'status_to' => $documentRequest->status->value,
                'description' => 'Admin mengunduh file dokumen',
            ]);

            return Storage::disk('documents')->download(
                $filePath,
                $documentRequest->file_name ?? $documentRequest->getFileName()
            );

        } catch (\Exception $e) {
            Log::error('Document download failed', [
                'document_id' => $id,
                'error' => $e->getMessage()
            ]);

            abort(500, 'Gagal mengunduh file');
        }
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('applicant_type')) {
            $query->where('applicant_type', $request->applicant_type);
        }

        if ($request->filled('delivery_method')) {
            $query->where('delivery_method', $request->delivery_method);
        }

        if ($request->filled('document_type_id')) {
            $query->where('document_type_id', $request->document_type_id);
        }

        if ($request->filled('has_file')) {
            if ($request->has_file == '1') {
                $query->whereNotNull('file_path');
            } else {
                $query->whereNull('file_path');
            }
        }

        if ($request->filled('verification_step')) {
            $query->where('current_verification_step', $request->verification_step);
        }

        if ($request->filled('signature_status')) {
            $query->where('signature_status', $request->signature_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('request_code', 'like', "%{$search}%")
                  ->orWhere('applicant_name', 'like', "%{$search}%")
                  ->orWhere('applicant_identifier', 'like', "%{$search}%")
                  ->orWhere('applicant_unit', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
    }

    private function getStatistics(): array
    {
        return [
            'total' => DocumentRequest::count(),
            'by_status' => [
                'pending' => DocumentRequest::where('status', 'pending')->count(),
                'approved' => DocumentRequest::where('status', 'approved')->count(),

                'verification_step_1_requested' => DocumentRequest::where('status', 'verification_step_1_requested')->count(),
                'verification_step_1_approved' => DocumentRequest::where('status', 'verification_step_1_approved')->count(),
                'verification_step_2_requested' => DocumentRequest::where('status', 'verification_step_2_requested')->count(),
                'verification_step_2_approved' => DocumentRequest::where('status', 'verification_step_2_approved')->count(),
                'verification_step_3_requested' => DocumentRequest::where('status', 'verification_step_3_requested')->count(),
                'verification_step_3_approved' => DocumentRequest::where('status', 'verification_step_3_approved')->count(),
                'verification_rejected' => DocumentRequest::where('status', 'verification_rejected')->count(),

                'processing' => DocumentRequest::where('status', 'processing')->count(),
                'waiting_signature' => DocumentRequest::where('status', 'waiting_signature')->count(),
                'signature_in_progress' => DocumentRequest::where('status', 'signature_in_progress')->count(),
                'signature_completed' => DocumentRequest::where('status', 'signature_completed')->count(),
                'signature_verified' => DocumentRequest::where('status', 'signature_verified')->count(),
                'ready_for_pickup' => DocumentRequest::where('status', 'ready_for_pickup')->count(),
                'picked_up' => DocumentRequest::where('status', 'picked_up')->count(),
                'completed' => DocumentRequest::where('status', 'completed')->count(),
                'rejected' => DocumentRequest::where('status', 'rejected')->count(),
            ],
            'by_applicant_type' => [
                'mahasiswa' => DocumentRequest::where('applicant_type', 'mahasiswa')->count(),
                'dosen' => DocumentRequest::where('applicant_type', 'dosen')->count(),
                'staff' => DocumentRequest::where('applicant_type', 'staff')->count(),
            ],
            'by_delivery' => [
                'pickup' => DocumentRequest::where('delivery_method', 'pickup')->count(),
                'download' => DocumentRequest::where('delivery_method', 'download')->count(),
            ],
            'by_verification_step' => [
                'not_started' => DocumentRequest::where('current_verification_step', 0)->count(),
                'level_1_in_progress' => DocumentRequest::where('current_verification_step', 1)
                    ->whereIn('status', ['verification_step_1_requested'])->count(),
                'level_2_in_progress' => DocumentRequest::where('current_verification_step', 2)
                    ->whereIn('status', ['verification_step_2_requested'])->count(),
                'level_3_in_progress' => DocumentRequest::where('current_verification_step', 3)
                    ->whereIn('status', ['verification_step_3_requested'])->count(),
                'all_verified' => DocumentRequest::where('current_verification_step', 3)
                    ->where('status', 'verification_step_3_approved')->count(),
            ],
            'signatures' => [
                'requiring_signature' => DocumentRequest::where('requires_signature', true)->count(),
                'waiting_signature' => DocumentRequest::where('status', 'waiting_signature')->count(),
                'signature_in_progress' => DocumentRequest::where('status', 'signature_in_progress')->count(),
                'signature_completed' => DocumentRequest::where('status', 'signature_completed')->count(),
            ],
            'with_files' => DocumentRequest::whereNotNull('file_path')->count(),
            'without_files' => DocumentRequest::whereNull('file_path')
                ->where('delivery_method', 'download')
                ->whereIn('status', ['approved', 'processing', 'ready_for_pickup'])
                ->count(),
            'today' => DocumentRequest::whereDate('created_at', today())->count(),
            'this_week' => DocumentRequest::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month' => DocumentRequest::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    public function getStats()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getStatistics()
        ]);
    }
}
