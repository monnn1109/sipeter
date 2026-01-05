<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Services\{DocumentDownloadService, DocumentHistoryService, NotificationService};
use App\Events\{DocumentDownloaded, DocumentCompleted};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentDownloadController extends Controller
{
    public function __construct(
        protected DocumentDownloadService $downloadService,
        protected DocumentHistoryService $historyService,
        protected NotificationService $notificationService
    ) {}

    public function download(DocumentRequest $documentRequest, Request $request)
    {
        $user = auth()->user();

        $canAccess = $documentRequest->user_id === $user->id
            || $user->role->value === 'admin'
            || ($user->isInternal() && $documentRequest->isInternalRequest());

        if (!$canAccess) {
            Log::warning('Unauthorized download attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role->value,
                'document_user_id' => $documentRequest->user_id,
                'document_id' => $documentRequest->id
            ]);

            abort(403, 'Anda tidak memiliki akses untuk mengunduh dokumen ini.');
        }

        if ($documentRequest->signature_status &&
            $documentRequest->signature_status !== 'ALL_SIGNATURES_COMPLETE') {
            return back()->with('error', 'Dokumen masih dalam proses tanda tangan. Mohon tunggu hingga selesai.');
        }

        $validation = $this->downloadService->validateDownload($documentRequest);

        if (!$validation['can_download']) {
            return back()->with('error', $validation['message']);
        }

        $result = $this->downloadService->downloadFile($documentRequest);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        event(new DocumentDownloaded($documentRequest, [
            'downloaded_by' => $user->name,
            'user_id' => $user->id,
            'user_role' => $user->role->value,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'downloaded_at' => now()->toDateTimeString(),
        ]));

        Log::info('Document downloaded by internal user', [
            'document_id' => $documentRequest->id,
            'request_code' => $documentRequest->request_code,
            'user_id' => $user->id,
            'user_name' => $user->name,
        ]);

        return $result['response'];
    }
    public function confirmDownload(DocumentRequest $documentRequest, Request $request)
    {
        try {
            $user = auth()->user();

            if ($documentRequest->user_id !== $user->id) {
                return back()->with('error', 'Anda tidak memiliki akses untuk mengkonfirmasi dokumen ini.');
            }

            if ($documentRequest->signature_status &&
                $documentRequest->signature_status !== 'ALL_SIGNATURES_COMPLETE') {
                return back()->with('error', 'Dokumen masih dalam proses tanda tangan. Mohon tunggu hingga selesai.');
            }

            if (!$documentRequest->isDownloadable()) {
                return back()->with('error', 'Dokumen belum siap untuk dikonfirmasi.');
            }

            if ($documentRequest->status->value === 'completed') {
                return back()->with('info', 'Dokumen sudah dikonfirmasi sebelumnya.');
            }

            $oldStatus = $documentRequest->status->value;
            $documentRequest->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $this->historyService->logCompleted($documentRequest);

            $this->historyService->logStatusChange(
                $documentRequest,
                $oldStatus,
                'completed',
                "{$user->name} mengkonfirmasi telah mengunduh dokumen"
            );

            event(new DocumentCompleted($documentRequest));

            $this->notifyAdminDownloadConfirmed($documentRequest, $user);

            Log::info('Internal user confirmed download', [
                'document_id' => $documentRequest->id,
                'request_code' => $documentRequest->request_code,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'ip_address' => $request->ip(),
            ]);

            return back()->with('success', 'Terima kasih! Konfirmasi Anda telah diterima. Proses pengajuan selesai.');

        } catch (\Exception $e) {
            Log::error('Internal confirm download failed', [
                'document_id' => $documentRequest->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Gagal mengkonfirmasi pengambilan dokumen.');
        }
    }

    public function preview(DocumentRequest $documentRequest)
    {
        $user = auth()->user();

        $canAccess = $documentRequest->user_id === $user->id
            || $user->role->value === 'admin'
            || ($user->isInternal() && $documentRequest->isInternalRequest());

        if (!$canAccess) {
            abort(403, 'Anda tidak memiliki akses untuk melihat dokumen ini.');
        }

        $validation = $this->downloadService->validateDownload($documentRequest);

        if (!$validation['can_download']) {
            return back()->with('error', $validation['message']);
        }

        $filePath = $documentRequest->file_path;

        if (!Storage::disk('documents')->exists($filePath)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $fullPath = Storage::disk('documents')->path($filePath);
        $mimeType = Storage::disk('documents')->mimeType($filePath);
        $fileName = $documentRequest->file_name ?? basename($filePath);

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }

    public function checkReadiness(DocumentRequest $documentRequest)
    {
        $user = auth()->user();

        $canAccess = $documentRequest->user_id === $user->id
            || $user->role->value === 'admin'
            || ($user->isInternal() && $documentRequest->isInternalRequest());

        if (!$canAccess) {
            return response()->json([
                'ready' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        $isDownloadable = $this->downloadService->isDownloadable($documentRequest);
        $validation = $this->downloadService->validateDownload($documentRequest);

        return response()->json([
            'ready' => $isDownloadable,
            'can_download' => $validation['can_download'],
            'is_completed' => $documentRequest->status->value === 'completed',
            'signature_status' => $documentRequest->signature_status,
            'signature_complete' => $documentRequest->signature_status === 'ALL_SIGNATURES_COMPLETE',
            'message' => $validation['message'],
            'stats' => $isDownloadable ? $this->downloadService->getDownloadStats($documentRequest) : null
        ]);
    }

    protected function notifyAdminDownloadConfirmed(DocumentRequest $documentRequest, $user): void
    {
        $admins = \App\Models\User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            \App\Models\AdminNotification::create([
                'user_id' => $admin->id,
                'type' => 'document_download_confirmed',
                'title' => '✅ Dokumen Dikonfirmasi Diambil',
                'message' => "{$user->name} ({$user->role->label()}) telah mengkonfirmasi pengambilan dokumen {$documentRequest->documentType->name} (Kode: {$documentRequest->request_code})",
                'document_request_id' => $documentRequest->id,
                'action_url' => route('admin.documents.show', $documentRequest->id),
                'icon' => 'check-circle',
                'color' => 'green',
            ]);
        }
    }
}
