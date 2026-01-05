<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Enums\NotificationType; // 🆕 ADD
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminNotification::where('user_id', auth()->id());

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = AdminNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        $stats = [
            'total' => AdminNotification::where('user_id', auth()->id())->count(),
            'unread' => $unreadCount,
            'read' => AdminNotification::where('user_id', auth()->id())
                ->where('is_read', true)->count(),

            'verification_requested' => AdminNotification::where('user_id', auth()->id())
                ->where('type', NotificationType::VERIFICATION_REQUESTED->value)->count(),
            'verification_approved' => AdminNotification::where('user_id', auth()->id())
                ->where('type', NotificationType::VERIFICATION_APPROVED->value)->count(),
            'verification_rejected' => AdminNotification::where('user_id', auth()->id())
                ->where('type', NotificationType::VERIFICATION_REJECTED->value)->count(),

            'signature_requested' => AdminNotification::where('user_id', auth()->id())
                ->where('type', NotificationType::SIGNATURE_REQUESTED->value)->count(),
            'signature_uploaded' => AdminNotification::where('user_id', auth()->id())
                ->where('type', NotificationType::SIGNATURE_UPLOADED->value)->count(),
            'signature_verified' => AdminNotification::where('user_id', auth()->id())
                ->where('type', NotificationType::SIGNATURE_VERIFIED->value)->count(),
        ];

        return view('admin.notifications.index', compact('notifications', 'unreadCount', 'stats'));
    }

    public function markAsRead($id)
    {
        $notification = AdminNotification::where('user_id', auth()->id())
            ->findOrFail($id);

        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notifikasi telah ditandai sebagai dibaca');
    }

    public function markAllAsRead()
    {
        AdminNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca');
    }

    public function delete($id)
    {
        $notification = AdminNotification::where('user_id', auth()->id())
            ->findOrFail($id);

        $notification->delete();

        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus');
    }

    public function getCount()
    {
        $unreadCount = AdminNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount
        ]);
    }

    public function getLatest()
    {
        $notifications = AdminNotification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type->value,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'icon' => $this->getNotificationIcon($notification->type),
                    'color' => $this->getNotificationColor($notification->type),
                ];
            });

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => AdminNotification::where('user_id', auth()->id())
                ->where('is_read', false)->count()
        ]);
    }

    private function getNotificationIcon(NotificationType $type): string
    {
        return match($type) {
            NotificationType::DOCUMENT_SUBMITTED => '📝',
            NotificationType::DOCUMENT_APPROVED => '✅',
            NotificationType::DOCUMENT_REJECTED => '❌',
            NotificationType::DOCUMENT_UPLOADED => '📄',
            NotificationType::DOCUMENT_READY => '📦',
            NotificationType::DOCUMENT_COMPLETED => '✔️',
            NotificationType::VERIFICATION_REQUESTED => '🔍',
            NotificationType::VERIFICATION_APPROVED => '✅',
            NotificationType::VERIFICATION_REJECTED => '❌',
            NotificationType::SIGNATURE_REQUESTED => '✍️',
            NotificationType::SIGNATURE_UPLOADED => '📝',
            NotificationType::SIGNATURE_VERIFIED => '✅',
            default => 'ℹ️',
        };
    }

    private function getNotificationColor(NotificationType $type): string
    {
        return match($type) {
            NotificationType::DOCUMENT_SUBMITTED => 'blue',
            NotificationType::DOCUMENT_APPROVED => 'green',
            NotificationType::DOCUMENT_REJECTED => 'red',
            NotificationType::DOCUMENT_UPLOADED => 'indigo',
            NotificationType::DOCUMENT_READY => 'purple',
            NotificationType::DOCUMENT_COMPLETED => 'gray',
            NotificationType::VERIFICATION_REQUESTED => 'cyan',
            NotificationType::VERIFICATION_APPROVED => 'green',
            NotificationType::VERIFICATION_REJECTED => 'red',
            NotificationType::SIGNATURE_REQUESTED => 'orange',
            NotificationType::SIGNATURE_UPLOADED => 'purple',
            NotificationType::SIGNATURE_VERIFIED => 'green',
            default => 'gray',
        };
    }
}
