<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     * Middleware untuk memastikan hanya Admin yang bisa akses
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        $user = Auth::user();

        // ✅ FIXED: Gunakan method isAdmin() dari model
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Jika Dosen atau Staff, redirect ke dashboard internal
        if ($user->isDosen() || $user->isStaff()) {
            return redirect()->route('internal.dashboard')
                ->with('warning', 'Anda tidak memiliki akses ke halaman admin');
        }

        // Jika bukan role yang valid, redirect ke home
        return redirect()->route('home')
            ->with('error', 'Akses ditolak. Hanya Admin yang diizinkan.');
    }
}
