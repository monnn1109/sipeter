<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckInternal
{
    /**
     * Handle an incoming request.
     * Middleware untuk memastikan hanya Dosen dan Staff yang bisa akses
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        $user = Auth::user();

        // Cek apakah user adalah Dosen atau Staff
        if ($user->isDosen() || $user->isStaff()) {
            return $next($request);
        }

        // Jika Admin, redirect ke dashboard admin
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('warning', 'Anda tidak memiliki akses ke halaman internal');
        }

        // Jika bukan role yang valid, logout dan redirect
        Auth::logout();
        return redirect()->route('auth.login')
            ->with('error', 'Akses ditolak. Role tidak valid.');
    }
}
