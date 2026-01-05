<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total' => DocumentRequest::where('user_id', $user->id)->count(),
            'pending' => DocumentRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'processing' => DocumentRequest::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'processing', 'ready_for_pickup'])
                ->count(),
            'completed' => DocumentRequest::where('user_id', $user->id)
                ->whereIn('status', ['completed', 'picked_up'])
                ->count(),

            'waiting_verification' => DocumentRequest::where('user_id', $user->id)
                ->where('status', 'verification_requested')                ->count(),
            'verified' => DocumentRequest::where('user_id', $user->id)
                ->where('status', 'verification_approved')
                ->count(),

            'waiting_signature' => DocumentRequest::where('user_id', $user->id)
                ->where('status', 'waiting_signature')
                ->count(),
            'signature_uploaded' => DocumentRequest::where('user_id', $user->id)
                ->where('status', 'signature_in_progress')
                ->count(),
            'signature_verified' => DocumentRequest::where('user_id', $user->id)
                ->where('status', 'signature_completed')
                ->count(),
        ];

        $recentDocuments = DocumentRequest::where('user_id', $user->id)
            ->with([
                'documentType',
                'documentVerification',
                'documentSignatures'
            ])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $waitingSignatureDocuments = DocumentRequest::where('user_id', $user->id)
            ->whereIn('status', ['waiting_signature', 'signature_in_progress'])
            ->with([
                'documentType',
                'documentSignatures.authority'
            ])
            ->orderBy('created_at', 'asc')
            ->limit(3)
            ->get();

        return view('internal.dashboard', compact(
            'stats',
            'recentDocuments',
            'waitingSignatureDocuments'
        ));
    }
}
