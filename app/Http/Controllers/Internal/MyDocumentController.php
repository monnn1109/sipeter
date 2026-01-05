<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Services\{DocumentDownloadService, DocumentHistoryService};
use App\Events\{DocumentMarkedAsTaken, DocumentCompleted};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log};

class MyDocumentController extends Controller
{
    public function __construct(
        protected DocumentDownloadService $downloadService,
        protected DocumentHistoryService $historyService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = DocumentRequest::where('user_id', $user->id)
            ->with([
                'documentType',
                'documentVerification.verifier',
                'documentSignatures.authority.user'
            ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }

        if ($request->filled('signature_status')) {
            $query->where('signature_status', $request->signature_status);
        }

        if ($request->filled('delivery_method')) {
            $query->where('delivery_method', $request->delivery_method);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('request_code', 'like', "%{$search}%")
                  ->orWhereHas('documentType', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $documents = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => DocumentRequest::where('user_id', $user->id)->count(),
            'pending' => DocumentRequest::where('user_id', $user->id)->pending()->count(),
            'approved' => DocumentRequest::where('user_id', $user->id)->approved()->count(),
            'ready' => DocumentRequest::where('user_id', $user->id)->ready()->count(),
            'completed' => DocumentRequest::where('user_id', $user->id)->completed()->count(),
            'rejected' => DocumentRequest::where('user_id', $user->id)->where('status', 'rejected')->count(),

            'waiting_verification' => DocumentRequest::where('user_id', $user->id)
                ->where('verification_status', 'verification_requested')->count(),
            'verified' => DocumentRequest::where('user_id', $user->id)
                ->where('verification_status', 'verification_approved')->count(),

            'waiting_signature' => DocumentRequest::where('user_id', $user->id)
                ->where('signature_status', 'signature_requested')->count(),
            'signature_uploaded' => DocumentRequest::where('user_id', $user->id)
                ->where('signature_status', 'signature_uploaded')->count(),
            'signature_verified' => DocumentRequest::where('user_id', $user->id)
                ->where('signature_status', 'signature_verified')->count(),
        ];

        return view('internal.my-documents', [
            'documents' => $documents,
            'stats' => $stats,
            'title' => 'Dokumen Saya',
            'active' => 'my-documents'
        ]);
    }

    public function show($id)
    {
        $documentRequest = DocumentRequest::with([
                'documentType',
                'activities.performedBy',
                'documentVerification.verifier.signatureAuthority',
                'documentSignatures.authority.user'
            ])
            ->findOrFail($id);

        if ($documentRequest->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

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
                    'download_url' => route('internal.my-documents.download', $documentRequest->id),
                    'can_mark_as_taken' => $documentRequest->canBeMarkedAsTaken(),
                    'is_marked_as_taken' => $documentRequest->is_marked_as_taken,
                    'marked_at' => $documentRequest->marked_as_taken_at?->format('d/m/Y H:i'),
                ];
            }
        }

        return view('internal.document-detail', [
            'documentRequest' => $documentRequest,
            'canDownload' => $canDownload,
            'downloadInfo' => $downloadInfo,
            'title' => 'Detail Dokumen - ' . $documentRequest->request_code,
            'active' => 'my-documents'
        ]);
    }

    public function markAsTaken($id, Request $request)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);
            $user = Auth::user();

            if ($documentRequest->user_id !== $user->id) {
                return back()->with('error', 'Anda tidak memiliki akses ke dokumen ini.');
            }

            if (!$documentRequest->canBeMarkedAsTaken()) {
                return back()->with('error', 'Dokumen tidak dapat dikonfirmasi saat ini.');
            }

            if ($documentRequest->is_marked_as_taken) {
                return back()->with('info', 'Dokumen sudah dikonfirmasi sebelumnya.');
            }

            DB::beginTransaction();

            $documentRequest->update([
                'is_marked_as_taken' => true,
                'marked_as_taken_at' => now(),
                'marked_as_taken_by' => $user->id,
                'marked_by_role' => 'user',
                'taken_notes' => $request->input('notes', 'User mengkonfirmasi telah menerima dokumen'),
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            event(new DocumentMarkedAsTaken($documentRequest, $user, 'user', $request->input('notes')));
            event(new DocumentCompleted($documentRequest));

            $this->historyService->logMarkedAsTaken($documentRequest, $user->name, $request->input('notes'));
            $this->historyService->logCompleted($documentRequest);

            DB::commit();

            Log::info('Internal user marked document as taken', [
                'document_id' => $documentRequest->id,
                'user_id' => $user->id,
            ]);

            return back()->with('success', 'Terima kasih! Konfirmasi Anda telah diterima.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Mark as taken failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal mengkonfirmasi.');
        }
    }

    public function getStats()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => DocumentRequest::where('user_id', $user->id)->count(),
                'by_status' => [
                    'pending' => DocumentRequest::where('user_id', $user->id)->pending()->count(),
                    'approved' => DocumentRequest::where('user_id', $user->id)->approved()->count(),
                    'waiting_verification' => DocumentRequest::where('user_id', $user->id)
                        ->where('verification_status', 'verification_requested')->count(),
                    'waiting_signature' => DocumentRequest::where('user_id', $user->id)
                        ->where('signature_status', 'signature_requested')->count(),
                    'ready' => DocumentRequest::where('user_id', $user->id)->ready()->count(),
                    'completed' => DocumentRequest::where('user_id', $user->id)->completed()->count(),
                ],
            ]
        ]);
    }

    public function checkDownload($id)
    {
        $documentRequest = DocumentRequest::findOrFail($id);

        if ($documentRequest->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['can_download' => false, 'message' => 'Unauthorized'], 403);
        }

        if (!$documentRequest->isDownloadDelivery()) {
            return response()->json(['can_download' => false, 'message' => 'Dokumen menggunakan metode Pickup']);
        }

        $validation = $this->downloadService->validateDownload($documentRequest);

        return response()->json([
            'can_download' => $validation['can_download'],
            'message' => $validation['message'],
            'can_mark_as_taken' => $documentRequest->canBeMarkedAsTaken(),
            'is_marked_as_taken' => $documentRequest->is_marked_as_taken,
        ]);
    }
}
