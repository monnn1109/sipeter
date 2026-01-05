<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppNotification;
use App\Models\DocumentRequest;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index(Request $request)
    {
        $query = WhatsAppNotification::with('documentRequest')
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('recipient_type')) {
            $query->where('recipient_type', $request->recipient_type);
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('recipient_phone', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%");
            });
        }

        $notifications = $query->paginate(20);

        $stats = [
            'total' => WhatsAppNotification::count(),
            'sent' => WhatsAppNotification::where('status', 'sent')->count(),
            'pending' => WhatsAppNotification::where('status', 'pending')->count(),
            'failed' => WhatsAppNotification::where('status', 'failed')->count(),
            'delivered' => WhatsAppNotification::where('status', 'delivered')->count(),
            'today' => WhatsAppNotification::whereDate('created_at', today())->count(),
        ];

        return view('admin.notifications.whatsapp-history', compact('notifications', 'stats'));
    }

    public function history(Request $request)
    {
        return $this->index($request);
    }

    public function show($id)
    {
        $notification = WhatsAppNotification::with('documentRequest.documentType')->findOrFail($id);

        return view('admin.notifications.whatsapp-detail', compact('notification'));
    }

    public function detail($id)
    {
        return $this->show($id);
    }

    public function resend($id)
    {
        try {
            $notification = WhatsAppNotification::findOrFail($id);

            if (!in_array($notification->status, ['failed', 'pending'])) {
                return back()->with('error', 'Hanya notifikasi yang gagal atau pending yang bisa dikirim ulang.');
            }

            if ($notification->retry_count >= 3) {
                return back()->with('error', 'Batas maksimal retry sudah tercapai (3x).');
            }

            $notification->update(['status' => 'pending']);

            $success = $this->whatsappService->sendMessage(
                $notification->recipient_phone,
                $notification->message
            );

            if ($success) {
                $notification->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'retry_count' => $notification->retry_count + 1,
                    'error_message' => null,
                ]);

                return back()->with('success', 'Notifikasi berhasil dikirim ulang!');
            } else {
                $notification->update([
                    'status' => 'failed',
                    'retry_count' => $notification->retry_count + 1,
                    'error_message' => 'Failed to resend message',
                ]);

                return back()->with('error', 'Gagal mengirim ulang notifikasi.');
            }

        } catch (\Exception $e) {
            Log::error('Resend WA notification failed', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function markAsRead($id)
    {
        try {
            $notification = WhatsAppNotification::findOrFail($id);

            $notification->update([
                'status' => 'read',
                'read_at' => now(),
            ]);

            return back()->with('success', 'Notifikasi ditandai sebagai dibaca.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate status.');
        }
    }

    public function delete($id)
    {
        try {
            $notification = WhatsAppNotification::findOrFail($id);
            $notification->delete();

            return back()->with('success', 'Notifikasi berhasil dihapus.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus notifikasi.');
        }
    }

    public function bulkResend(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:whatsapp_notifications,id'
        ]);

        try {
            $notifications = WhatsAppNotification::whereIn('id', $request->ids)
                ->whereIn('status', ['failed', 'pending'])
                ->where('retry_count', '<', 3)
                ->get();

            $successCount = 0;
            $failedCount = 0;

            foreach ($notifications as $notification) {
                $success = $this->whatsappService->sendMessage(
                    $notification->recipient_phone,
                    $notification->message
                );

                if ($success) {
                    $notification->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                        'retry_count' => $notification->retry_count + 1,
                        'error_message' => null,
                    ]);
                    $successCount++;
                } else {
                    $notification->update([
                        'status' => 'failed',
                        'retry_count' => $notification->retry_count + 1,
                        'error_message' => 'Failed to resend message',
                    ]);
                    $failedCount++;
                }
            }

            return back()->with('success', "Berhasil kirim ulang {$successCount} notifikasi. Gagal: {$failedCount}");

        } catch (\Exception $e) {
            Log::error('Bulk resend failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat bulk resend.');
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:whatsapp_notifications,id'
        ]);

        try {
            WhatsAppNotification::whereIn('id', $request->ids)->delete();

            return back()->with('success', count($request->ids) . ' notifikasi berhasil dihapus.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus notifikasi.');
        }
    }

    public function byDocument($documentId)
    {
        $document = DocumentRequest::findOrFail($documentId);

        $notifications = WhatsAppNotification::where('document_request_id', $documentId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.notifications.whatsapp-history', compact('notifications', 'document'));
    }

    public function byStatus($status)
    {
        $notifications = WhatsAppNotification::where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => WhatsAppNotification::count(),
            'sent' => WhatsAppNotification::where('status', 'sent')->count(),
            'pending' => WhatsAppNotification::where('status', 'pending')->count(),
            'failed' => WhatsAppNotification::where('status', 'failed')->count(),
            'delivered' => WhatsAppNotification::where('status', 'delivered')->count(),
        ];

        return view('admin.notifications.whatsapp-history', compact('notifications', 'stats', 'status'));
    }

    public function byType($type)
    {
        $notifications = WhatsAppNotification::where('event_type', $type)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.notifications.whatsapp-history', compact('notifications', 'type'));
    }

    public function stats()
    {
        $stats = [
            'total' => WhatsAppNotification::count(),
            'today' => WhatsAppNotification::whereDate('created_at', today())->count(),
            'this_week' => WhatsAppNotification::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => WhatsAppNotification::whereMonth('created_at', now()->month)->count(),
            'sent' => WhatsAppNotification::where('status', 'sent')->count(),
            'pending' => WhatsAppNotification::where('status', 'pending')->count(),
            'failed' => WhatsAppNotification::where('status', 'failed')->count(),
            'delivered' => WhatsAppNotification::where('status', 'delivered')->count(),
            'read' => WhatsAppNotification::where('status', 'read')->count(),
            'success_rate' => WhatsAppNotification::count() > 0
                ? round((WhatsAppNotification::whereIn('status', ['sent', 'delivered', 'read'])->count() / WhatsAppNotification::count()) * 100, 2)
                : 0,
        ];

        return response()->json($stats);
    }

    public function dailyStats(Request $request)
    {
        $days = $request->input('days', 7);

        $stats = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $stats[] = [
                'date' => $date,
                'total' => WhatsAppNotification::whereDate('created_at', $date)->count(),
                'sent' => WhatsAppNotification::whereDate('created_at', $date)->where('status', 'sent')->count(),
                'failed' => WhatsAppNotification::whereDate('created_at', $date)->where('status', 'failed')->count(),
            ];
        }

        return response()->json($stats);
    }

    public function statsByEvent()
    {
        $stats = WhatsAppNotification::selectRaw('event_type, COUNT(*) as count')
            ->groupBy('event_type')
            ->orderBy('count', 'desc')
            ->get();

        return response()->json($stats);
    }

    public function exportExcel(Request $request)
    {
        return back()->with('info', 'Fitur export Excel sedang dalam pengembangan.');
    }

    public function exportPdf(Request $request)
    {
        return back()->with('info', 'Fitur export PDF sedang dalam pengembangan.');
    }

    public function gatewayStatus()
    {
        $isConnected = $this->whatsappService->isConnected();

        return response()->json([
            'connected' => $isConnected,
            'status' => $isConnected ? 'online' : 'offline',
            'message' => $isConnected ? 'WhatsApp Gateway is connected' : 'WhatsApp Gateway is offline',
        ]);
    }

    public function getQrCode()
    {
        $qrCode = $this->whatsappService->getQrCode();

        if ($qrCode) {
            return response($qrCode)->header('Content-Type', 'image/png');
        }

        return response()->json([
            'error' => 'QR Code not available. Gateway might be already connected.',
        ], 404);
    }
}
