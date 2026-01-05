<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\DocumentRequest;

class CheckDocumentOwner
{
    /**
     * Handle an incoming request.
     *
     * Middleware untuk memastikan user hanya bisa akses dokumen miliknya sendiri
     * - Admin: Bisa akses semua dokumen
     * - Internal (Dosen/Staff): Hanya dokumen miliknya (user_id match)
     * - Guest (Mahasiswa): Validasi via email atau request_code di session
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get document ID from route parameter
        $documentId = $request->route('id') ?? $request->route('documentRequest');

        // If route parameter is ID, fetch the document
        if (is_numeric($documentId)) {
            $documentRequest = DocumentRequest::findOrFail($documentId);
        } elseif ($documentId instanceof DocumentRequest) {
            $documentRequest = $documentId;
        } else {
            abort(404, 'Dokumen tidak ditemukan');
        }

        // ============================================
        // 1. ADMIN - Can access ALL documents
        // ============================================
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        // ============================================
        // 2. INTERNAL USER (Dosen/Staff) - Only THEIR documents
        // ============================================
        if (auth()->check() && auth()->user()->isInternal()) {
            // Check if user is the owner
            if ($documentRequest->user_id === auth()->id()) {
                return $next($request);
            }

            // Not the owner
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        // ============================================
        // 3. GUEST/MAHASISWA - Validate via session or email
        // ============================================

        // Cek apakah ini request dari mahasiswa (guest)
        if ($documentRequest->isGuestRequest()) {

            // Option A: Validasi via Session (dari tracking)
            // Ketika mahasiswa tracking dokumen, kita simpan request_code di session
            $sessionRequestCode = session('tracking_request_code');
            if ($sessionRequestCode && $sessionRequestCode === $documentRequest->request_code) {
                return $next($request);
            }

            // Option B: Validasi via Email di Query String (untuk download link di email/WA)
            // URL: /download/document/123?email=mahasiswa@example.com
            $emailParam = $request->query('email');
            if ($emailParam && $emailParam === $documentRequest->applicant_email) {
                // Set session untuk subsequent requests
                session(['tracking_request_code' => $documentRequest->request_code]);
                return $next($request);
            }

            // Option C: Validasi via Request Code di Query String
            // URL: /download/document/123?code=REQ-202410-0001
            $codeParam = $request->query('code');
            if ($codeParam && $codeParam === $documentRequest->request_code) {
                // Set session untuk subsequent requests
                session(['tracking_request_code' => $documentRequest->request_code]);
                return $next($request);
            }

            // Option D: Redirect ke tracking page jika belum valid
            // Redirect mahasiswa untuk input tracking code dulu
            return redirect()->route('mahasiswa.tracking.index')
                ->with('error', 'Silakan masukkan kode tracking terlebih dahulu untuk mengakses dokumen ini.')
                ->with('required_code', $documentRequest->request_code);
        }

        // ============================================
        // 4. FALLBACK - No valid access
        // ============================================
        abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
    }

    /**
     * ✅ Helper: Validate guest access via email verification
     * Bisa dipanggil dari controller untuk extra validation
     */
    public static function validateGuestAccess(DocumentRequest $documentRequest, string $email): bool
    {
        return $documentRequest->isGuestRequest()
            && $documentRequest->applicant_email === $email;
    }

    /**
     * ✅ Helper: Set tracking session for guest
     * Dipanggil setelah user berhasil tracking
     */
    public static function setTrackingSession(DocumentRequest $documentRequest): void
    {
        session([
            'tracking_request_code' => $documentRequest->request_code,
            'tracking_email' => $documentRequest->applicant_email,
            'tracking_validated_at' => now()->toDateTimeString(),
        ]);
    }

    /**
     * ✅ Helper: Clear tracking session
     */
    public static function clearTrackingSession(): void
    {
        session()->forget([
            'tracking_request_code',
            'tracking_email',
            'tracking_validated_at',
        ]);
    }

    /**
     * ✅ Helper: Check if guest has valid tracking session
     */
    public static function hasValidTrackingSession(string $requestCode): bool
    {
        $sessionCode = session('tracking_request_code');
        $validatedAt = session('tracking_validated_at');

        // Session valid for 24 hours
        if ($sessionCode === $requestCode && $validatedAt) {
            $validatedTime = \Carbon\Carbon::parse($validatedAt);
            return $validatedTime->diffInHours(now()) < 24;
        }

        return false;
    }
}
