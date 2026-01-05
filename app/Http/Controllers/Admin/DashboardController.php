<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{DocumentRequest, DocumentType, DocumentSignature, DocumentVerification};
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => DocumentRequest::count(),
            'pending' => DocumentRequest::where('status', 'pending')->count(),
            'approved' => DocumentRequest::where('status', 'approved')->count(),
            'ready' => DocumentRequest::where('status', 'ready_for_pickup')->count(),
            'completed' => DocumentRequest::whereIn('status', ['completed', 'picked_up'])->count(),

            // FIX: Query verification via relation
            'waiting_verification' => DocumentRequest::whereHas('documentVerification', function($q) {
                $q->where('status', 'requested');
            })->count(),

            'waiting_signature' => DocumentRequest::where('status', 'waiting_signature')->count(),
            'signature_in_progress' => DocumentRequest::where('status', 'signature_in_progress')->count(),
        ];

        $recentDocuments = DocumentRequest::with([
                'documentType',
                'user',
                'documentVerification',
                'documentSignatures'
            ])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $documentTypeStats = DocumentRequest::select('document_type_id', DB::raw('count(*) as count'))
            ->groupBy('document_type_id')
            ->with('documentType:id,name')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'name' => $item->documentType->name ?? 'Unknown',
                    'count' => $item->count
                ];
            });

        $applicantTypeStats = DocumentRequest::select('applicant_type', DB::raw('count(*) as count'))
            ->groupBy('applicant_type')
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'applicant_type' => $item->applicant_type,
                    'count' => $item->count
                ];
            });

        $signatureStats = [
            'total_pending' => DocumentSignature::pending()->count(),
            'awaiting_upload' => DocumentSignature::requested()->count(),
            'awaiting_verification' => DocumentSignature::uploaded()->count(),
            'verified_today' => DocumentSignature::verified()
                ->whereDate('verified_at', today())
                ->count(),
            'overdue' => DocumentSignature::requested()
                ->where('requested_at', '<', now()->subHours(24))
                ->count(),
        ];

        $recentSignatures = DocumentSignature::with([
                'documentRequest.documentType',
                'signatureAuthority'
            ])
            ->whereIn('status', ['uploaded', 'verified'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        $documentsRequiringAction = [
            'pending_approval' => DocumentRequest::where('status', 'pending')->count(),

            // FIX: Query verification via relation
            'pending_verification' => DocumentRequest::whereHas('documentVerification', function($q) {
                $q->where('status', 'requested');
            })->count(),

            'pending_signature' => DocumentRequest::whereIn('status', [
                'waiting_signature',
                'signature_in_progress'
            ])->count(),
            'ready_for_upload' => DocumentRequest::where('status', 'signature_completed')
                ->whereNull('file_path')
                ->count(),
            'without_files' => DocumentRequest::whereNull('file_path')
                ->where('delivery_method', 'download')
                ->whereIn('status', ['approved', 'processing'])
                ->count(),
        ];

        $statusChartData = [
            'labels' => ['Pending', 'Menunggu TTD', 'Proses TTD', 'Siap Diambil', 'Selesai'],
            'data' => [
                DocumentRequest::where('status', 'pending')->count(),
                DocumentRequest::where('status', 'waiting_signature')->count(),
                DocumentRequest::where('status', 'signature_in_progress')->count(),
                DocumentRequest::where('status', 'ready_for_pickup')->count(),
                DocumentRequest::whereIn('status', ['completed', 'picked_up'])->count(),
            ],
        ];

        return view('admin.dashboard', compact(
            'stats',
            'recentDocuments',
            'documentTypeStats',
            'applicantTypeStats',
            'signatureStats',
            'recentSignatures',
            'documentsRequiringAction',
            'statusChartData'
        ));
    }

    public function getStats()
    {
        $stats = [
            'documents' => [
                'total' => DocumentRequest::count(),
                'today' => DocumentRequest::whereDate('created_at', today())->count(),
                'this_week' => DocumentRequest::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'this_month' => DocumentRequest::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
            ],
            'by_status' => [
                'pending' => DocumentRequest::where('status', 'pending')->count(),
                'approved' => DocumentRequest::where('status', 'approved')->count(),
                'waiting_signature' => DocumentRequest::where('status', 'waiting_signature')->count(),
                'signature_in_progress' => DocumentRequest::where('status', 'signature_in_progress')->count(),
                'signature_completed' => DocumentRequest::where('status', 'signature_completed')->count(),
                'ready' => DocumentRequest::where('status', 'ready_for_pickup')->count(),
                'completed' => DocumentRequest::whereIn('status', ['completed', 'picked_up'])->count(),
                'rejected' => DocumentRequest::where('status', 'rejected')->count(),
            ],
            'verifications' => [
                // FIX: Query via DocumentVerification model
                'total' => DocumentVerification::count(),
                'requested' => DocumentVerification::where('status', 'requested')->count(),
                'approved' => DocumentVerification::where('status', 'approved')->count(),
                'rejected' => DocumentVerification::where('status', 'rejected')->count(),
            ],
            'signatures' => [
                'total_pending' => DocumentSignature::pending()->count(),
                'requested' => DocumentSignature::requested()->count(),
                'uploaded' => DocumentSignature::uploaded()->count(),
                'verified' => DocumentSignature::verified()->count(),
                'rejected' => DocumentSignature::rejected()->count(),
            ],
            'requiring_action' => [
                'pending_approval' => DocumentRequest::where('status', 'pending')->count(),

                // FIX: Query verification via relation
                'pending_verification' => DocumentRequest::whereHas('documentVerification', function($q) {
                    $q->where('status', 'requested');
                })->count(),

                'pending_signature' => DocumentRequest::whereIn('status', [
                    'waiting_signature',
                    'signature_in_progress'
                ])->count(),
                'signature_verification' => DocumentSignature::uploaded()->count(),
                'ready_for_upload' => DocumentRequest::where('status', 'signature_completed')
                    ->whereNull('file_path')
                    ->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function getQuickActions()
    {
        $actions = [
            [
                'title' => 'Dokumen Pending',
                'count' => DocumentRequest::where('status', 'pending')->count(),
                'url' => route('admin.documents.pending'),
                'icon' => 'clock',
                'color' => 'yellow',
            ],
            [
                'title' => 'Perlu Verifikasi',
                // FIX: Query verification via relation
                'count' => DocumentRequest::whereHas('documentVerification', function($q) {
                    $q->where('status', 'requested');
                })->count(),
                'url' => route('admin.verifications.pending'),
                'icon' => 'shield-check',
                'color' => 'blue',
            ],
            [
                'title' => 'TTD Perlu Diverifikasi',
                'count' => DocumentSignature::uploaded()->count(),
                'url' => route('admin.signatures.verify'),
                'icon' => 'check-square',
                'color' => 'purple',
            ],
            [
                'title' => 'Menunggu TTD',
                'count' => DocumentSignature::requested()->count(),
                'url' => route('admin.signatures.pending'),
                'icon' => 'edit-3',
                'color' => 'indigo',
            ],
            [
                'title' => 'Siap Upload Final',
                'count' => DocumentRequest::where('status', 'signature_completed')
                    ->whereNull('file_path')
                    ->count(),
                'url' => route('admin.documents.index', ['status' => 'signature_completed']),
                'icon' => 'upload',
                'color' => 'blue',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $actions
        ]);
    }
}
