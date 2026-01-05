<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Services\{DocumentDownloadService, DocumentHistoryService, NotificationService};
use App\Events\{DocumentDownloaded, DocumentCompleted, DocumentMarkedAsTaken};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Storage, Log, DB};

class DocumentDownloadController extends Controller
{
    public function __construct(
        protected DocumentDownloadService $downloadService,
        protected DocumentHistoryService $historyService,
        protected NotificationService $notificationService
    ) {}

    public function download($id, Request $request)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            if (!$documentRequest->isGuestRequest()) {
                abort(403, 'Dokumen ini tidak dapat diakses melalui halaman ini.');
            }

            if ($documentRequest->signature_status &&
                $documentRequest->signature_status !== 'ALL_SIGNATURES_COMPLETE') {
                return back()->with('error', 'Dokumen masih dalam proses tanda tangan. Mohon tunggu hingga selesai.');
            }

            if (!$documentRequest->hasFile()) {
                return back()->with('error', 'File dokumen belum tersedia. Silakan cek kembali nanti.');
            }

            if (!$documentRequest->isDownloadable()) {
                return back()->with('error', 'Dokumen belum siap untuk didownload. Status: ' . $documentRequest->status->label());
            }

            $filePath = $documentRequest->file_path;

            if (!Storage::disk('documents')->exists($filePath)) {
                Log::error('File not found in storage', [
                    'document_id' => $documentRequest->id,
                    'file_path' => $filePath
                ]);
                return back()->with('error', 'File tidak ditemukan di sistem. Silakan hubungi admin.');
            }

            event(new DocumentDownloaded($documentRequest, [
                'downloaded_by' => $documentRequest->applicant_name ?? 'Mahasiswa',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'downloaded_at' => now()->toDateTimeString(),
            ]));

            Log::info('Document downloaded by guest', [
                'document_id' => $documentRequest->id,
                'request_code' => $documentRequest->request_code,
                'applicant_name' => $documentRequest->applicant_name,
                'ip_address' => $request->ip(),
            ]);

            return Storage::disk('documents')->download(
                $filePath,
                $documentRequest->file_name ?? $documentRequest->getFileName()
            );

        } catch (\Exception $e) {
            Log::error('Guest download failed', [
                'document_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Gagal mengunduh dokumen: ' . $e->getMessage());
        }
    }

    public function markAsTaken($id, Request $request)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            if (!$documentRequest->isGuestRequest()) {
                return back()->with('error', 'Dokumen ini tidak dapat dikonfirmasi.');
            }

            if ($documentRequest->signature_status &&
                $documentRequest->signature_status !== 'ALL_SIGNATURES_COMPLETE') {
                return back()->with('error', 'Dokumen masih dalam proses tanda tangan. Mohon tunggu hingga selesai.');
            }

            if (!$documentRequest->isDownloadable()) {
                return back()->with('error', 'Dokumen belum siap untuk dikonfirmasi.');
            }

            if ($documentRequest->is_marked_as_taken) {
                return back()->with('info', 'Dokumen sudah dikonfirmasi sebelumnya pada ' . $documentRequest->marked_as_taken_at->format('d/m/Y H:i'));
            }

            DB::beginTransaction();

            $documentRequest->update([
                'is_marked_as_taken' => true,
                'marked_as_taken_at' => now(),
                'marked_as_taken_by' => null,
                'marked_by_role' => 'user',
                'taken_notes' => 'Mahasiswa mengkonfirmasi telah mengunduh dokumen',
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            event(new DocumentMarkedAsTaken(
                $documentRequest,
                null,
                'user',
                'Mahasiswa mengkonfirmasi telah mengunduh dokumen'
            ));

            $this->historyService->logMarkedAsTaken(
                $documentRequest,
                $documentRequest->applicant_name ?? 'Mahasiswa',
                'Mahasiswa mengkonfirmasi telah mengunduh dokumen'
            );

            $this->historyService->logCompleted($documentRequest);

            event(new DocumentCompleted($documentRequest));

            $this->notifyAdminDocumentMarkedAsTaken($documentRequest);

            DB::commit();

            Log::info('Guest marked document as taken', [
                'document_id' => $documentRequest->id,
                'request_code' => $documentRequest->request_code,
                'applicant_name' => $documentRequest->applicant_name,
                'ip_address' => $request->ip(),
            ]);

            return back()->with('success', 'Terima kasih! Konfirmasi Anda telah diterima. Proses pengajuan selesai.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Guest mark as taken failed', [
                'document_id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Gagal mengkonfirmasi pengambilan dokumen.');
        }
    }

    public function preview($id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            if (!$documentRequest->isGuestRequest()) {
                abort(403, 'Dokumen ini tidak dapat diakses.');
            }

            if (!$documentRequest->hasFile()) {
                abort(404, 'File tidak ditemukan');
            }

            if (!$documentRequest->isDownloadable()) {
                abort(403, 'Dokumen belum siap untuk diakses');
            }

            $filePath = $documentRequest->file_path;

            if (!Storage::disk('documents')->exists($filePath)) {
                abort(404, 'File tidak ditemukan di storage');
            }

            $mimeType = Storage::disk('documents')->mimeType($filePath);
            $fileName = $documentRequest->file_name ?? $documentRequest->getFileName();

            return response()->file(
                Storage::disk('documents')->path($filePath),
                [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"'
                ]
            );

        } catch (\Exception $e) {
            Log::error('Guest preview failed', [
                'document_id' => $id,
                'error' => $e->getMessage()
            ]);

            abort(500, 'Gagal menampilkan file');
        }
    }

    public function checkReadiness($id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            if (!$documentRequest->isGuestRequest()) {
                return response()->json([
                    'ready' => false,
                    'message' => 'Dokumen tidak dapat diakses'
                ], 403);
            }

            $ready = $documentRequest->hasFile() && $documentRequest->isDownloadable();

            return response()->json([
                'ready' => $ready,
                'status' => $documentRequest->status->value,
                'status_label' => $documentRequest->status->label(),
                'has_file' => $documentRequest->hasFile(),
                'is_completed' => $documentRequest->status->value === 'completed',
                'is_marked_as_taken' => $documentRequest->is_marked_as_taken,
                'marked_at' => $documentRequest->marked_as_taken_at?->format('d/m/Y H:i'),
                'can_mark_as_taken' => $documentRequest->canBeMarkedAsTaken(),
                'signature_status' => $documentRequest->signature_status,
                'signature_complete' => $documentRequest->signature_status === 'ALL_SIGNATURES_COMPLETE',
                'message' => $ready
                    ? 'Dokumen siap didownload'
                    : 'Dokumen belum siap. Status: ' . $documentRequest->status->label()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ready' => false,
                'message' => 'Terjadi kesalahan'
            ], 500);
        }
    }

    protected function notifyAdminDocumentMarkedAsTaken(DocumentRequest $documentRequest): void
    {
        $this->notificationService->notifyAdminDocumentMarkedAsTaken(
            $documentRequest,
            $documentRequest->applicant_name ?? 'Mahasiswa'
        );
    }
}
