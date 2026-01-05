<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        // Jika sudah login, redirect ke dashboard sesuai role
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Email/NIP/NIDN harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        // Tentukan field login (email, nip, atau nidn)
        $loginField = $this->determineLoginField($request->login);

        // Credentials untuk authentication
        $credentials = [
            $loginField => $request->login,
            'password' => $request->password,
        ];

        // Remember me
        $remember = $request->filled('remember');

        // ✅ Debug log
        \Log::info('Login attempt', [
            'field' => $loginField,
            'value' => $request->login,
            'remember' => $remember
        ]);

        // Attempt login
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // ✅ Debug log
            \Log::info('Login successful', [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]);

            // Redirect berdasarkan role
            return $this->redirectBasedOnRole();
        }

        // Login gagal
        \Log::warning('Login failed', [
            'field' => $loginField,
            'value' => $request->login
        ]);

        throw ValidationException::withMessages([
            'login' => 'Email/NIP/NIDN atau password salah.',
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('success', 'Berhasil logout');
    }

    /**
     * Tentukan field login berdasarkan input
     *
     * @param string $login
     * @return string 'email' atau 'nip_nidn'
     */
    private function determineLoginField(string $login): string
    {
        // Jika mengandung @, maka email
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        // Selain email, pakai nip_nidn (gabungan NIP/NIDN)
        return 'nip_nidn';
    }

    /**
     * Redirect berdasarkan role user
     */
    private function redirectBasedOnRole()
    {
        $user = Auth::user();

        // ✅ Debug log
        \Log::info('Redirecting user', [
            'user_id' => $user->id,
            'role' => $user->role,
            'isAdmin' => $user->isAdmin(),
            'isDosen' => $user->isDosen(),
            'isStaff' => $user->isStaff()
        ]);

        // Admin -> Admin Dashboard
        if ($user->isAdmin()) {
            \Log::info('Redirecting to admin dashboard');
            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Selamat datang, ' . $user->name);
        }

        // Dosen/Staff -> Internal Dashboard
        if ($user->isDosen() || $user->isStaff()) {
            \Log::info('Redirecting to internal dashboard');
            return redirect()
                ->route('internal.dashboard')
                ->with('success', 'Selamat datang, ' . $user->name);
        }

        // Jika tidak punya role yang valid, logout
        \Log::error('Invalid role detected', ['role' => $user->role]);
        Auth::logout();
        return redirect()
            ->route('guest.login.submit')
            ->with('error', 'Role tidak valid. Silakan hubungi administrator.');
    }
}
