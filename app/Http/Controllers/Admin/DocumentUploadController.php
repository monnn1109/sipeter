<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentUploadRequest;
use App\Models\DocumentRequest;
use App\Services\{DocumentUploadService, DocumentHistoryService};
use App\Events\DocumentUploaded;
use App\Enums\DocumentStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Storage, Log, DB};
use Symfony\Component\HttpFoundation\StreamedResponse;
use Exception;

class DocumentUploadController extends Controller
{
    protected DocumentUploadService $uploadService;
    protected DocumentHistoryService $historyService;

    public function __construct(
        DocumentUploadService $uploadService,
        DocumentHistoryService $historyService
    ) {
        $this->uploadService = $uploadService;
        $this->historyService = $historyService;
    }

    public function upload(DocumentUploadRequest $request, $id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            if ($documentRequest->hasFile()) {
                return $this->redirectWithError(
                    $documentRequest,
                    'Dokumen sudah memiliki file. Gunakan fitur Replace untuk mengganti file.'
                );
            }

            $file = $request->file('document_file');
            $result = $this->uploadService->upload($documentRequest, $file);

            if ($result['success']) {
                event(new DocumentUploaded($documentRequest->fresh()));

                return $this->redirectWithSuccess(
                    $documentRequest,
                    $result['message'] ?? 'File dokumen berhasil diupload!'
                );
            }

            return $this->redirectWithError($documentRequest, $result['message']);

        } catch (Exception $e) {
            Log::error('Document upload failed', [
                'document_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', 'Gagal mengupload file: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * 🔥 NEW: Submit Final Document (Sudah Ter-embed 3 TTD)
     * Route: POST /admin/upload/{id}/submit
     * Called from: detail.blade.php - Upload Final Document Modal
     */
    public function submitFinalDocument(Request $request, $id)
    {
        $request->validate([
            'document_file' => 'required|file|mimes:pdf|max:10240', // Max 10MB
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $documentRequest = DocumentRequest::with('signatures')->findOrFail($id);

            // ✅ CHECK 1: Semua TTD harus verified (Level 1, 2, 3)
            $verifiedSignatures = $documentRequest->signatures()
                ->where('status', 'verified')
                ->count();

            if ($verifiedSignatures < 3) {
                DB::rollBack();

                Log::warning('Final document upload - Not all signatures verified', [
                    'document_id' => $id,
                    'verified_count' => $verifiedSignatures,
                    'required_count' => 3
                ]);

                return back()->with('error', '❌ Belum semua TTD diverifikasi! Verified: ' . $verifiedSignatures . '/3');
            }

            // ✅ CHECK 2: Jangan upload 2x
            if ($documentRequest->hasFile()) {
                DB::rollBack();
                return back()->with('error', '⚠️ Dokumen final sudah diupload sebelumnya!');
            }

            // ✅ UPLOAD: File PDF final
            $file = $request->file('document_file');
            $result = $this->uploadService->upload($documentRequest, $file);

            if (!$result['success']) {
                DB::rollBack();
                return $this->redirectWithError($documentRequest, $result['message']);
            }

            Log::info('Final document uploaded successfully', [
                'document_id' => $id,
                'file_name' => $documentRequest->file_name,
                'file_size' => $documentRequest->file_size,
                'admin_id' => auth()->id()
            ]);

            // ✅ UPDATE: Status to READY_FOR_PICKUP
            $documentRequest->update([
                'status' => DocumentStatus::READY_FOR_PICKUP,
                'admin_notes' => $request->notes ?? 'Dokumen final sudah ter-embed dengan 3 TTD',
            ]);

            // ✅ LOG: Activity ke riwayat
            $this->historyService->logFinalDocumentUploaded($documentRequest);

            Log::info('Final document status updated', [
                'document_id' => $id,
                'new_status' => DocumentStatus::READY_FOR_PICKUP->value
            ]);

            // ✅ EVENT: Fire notifikasi ke user
            event(new DocumentUploaded($documentRequest->fresh()));

            DB::commit();

            return redirect()
                ->route('admin.documents.show', $documentRequest->id)
                ->with('success', '🎉 Dokumen final berhasil diupload! Status: Siap Diambil. Notifikasi WA sudah dikirim ke pemohon.');

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Final document upload failed', [
                'document_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', '❌ Gagal mengupload dokumen final: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function replace(DocumentUploadRequest $request, $id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            if (!$documentRequest->hasFile()) {
                return $this->redirectWithError(
                    $documentRequest,
                    'Tidak ada file yang dapat diganti. Silakan upload file terlebih dahulu.'
                );
            }

            $file = $request->file('document_file');
            $result = $this->uploadService->replace($documentRequest, $file);

            if ($result['success']) {
                event(new DocumentUploaded($documentRequest->fresh()));

                return $this->redirectWithSuccess(
                    $documentRequest,
                    $result['message'] ?? 'File dokumen berhasil diganti!'
                );
            }

            return $this->redirectWithError($documentRequest, $result['message']);

        } catch (Exception $e) {
            Log::error('Document replace failed', [
                'document_id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()
                ->with('error', 'Gagal mengganti file: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            if (!$documentRequest->hasFile()) {
                return $this->redirectWithError(
                    $documentRequest,
                    'Tidak ada file yang dapat dihapus.'
                );
            }

            $result = $this->uploadService->delete($documentRequest);

            if ($result['success']) {
                return $this->redirectWithSuccess(
                    $documentRequest,
                    $result['message'] ?? 'File dokumen berhasil dihapus!'
                );
            }

            return $this->redirectWithError($documentRequest, $result['message']);

        } catch (Exception $e) {
            Log::error('Document delete failed', [
                'document_id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Gagal menghapus file: ' . $e->getMessage());
        }
    }

    public function preview($id)
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

            $mimeType = Storage::disk('documents')->mimeType($filePath);
            $fileName = $documentRequest->file_name;

            return response()->file(
                Storage::disk('documents')->path($filePath),
                [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"'
                ]
            );

        } catch (Exception $e) {
            Log::error('Document preview failed', [
                'document_id' => $id,
                'error' => $e->getMessage()
            ]);

            abort(500, 'Gagal menampilkan file');
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

            return Storage::disk('documents')->download(
                $filePath,
                $documentRequest->file_name
            );

        } catch (Exception $e) {
            Log::error('Document download failed', [
                'document_id' => $id,
                'error' => $e->getMessage()
            ]);

            abort(500, 'Gagal mengunduh file');
        }
    }

    public function getFileInfo($id): JsonResponse
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            if (!$documentRequest->hasFile()) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            $fileInfo = $this->uploadService->getFileInfo($documentRequest);

            return response()->json([
                'success' => true,
                'data' => $fileInfo
            ]);

        } catch (Exception $e) {
            Log::error('Get file info failed', [
                'document_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan informasi file'
            ], 500);
        }
    }

    protected function redirectWithSuccess(DocumentRequest $document, string $message)
    {
        return redirect()
            ->route('admin.documents.show', $document->id)
            ->with('success', $message);
    }

    protected function redirectWithError(DocumentRequest $document, string $message)
    {
        return back()
            ->with('error', $message)
            ->withInput();
    }
}
