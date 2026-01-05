<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show profile page
     * GET /internal/profile
     */
    public function edit()
    {
        $user = auth()->user();

        return view('internal.profile', [
            'user' => $user,
            'title' => 'Profile Saya',
            'active' => 'profile'
        ]);
    }

    /**
     * Update profile
     * PUT /internal/profile
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20', // ✅ FIXED
            'unit' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama harus diisi',
            'phone_number.required' => 'Nomor telepon harus diisi', // ✅ FIXED
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile berhasil diupdate');
    }

    /**
     * Update password
     * POST /internal/profile/change-password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Password lama harus diisi',
            'password.required' => 'Password baru harus diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password lama tidak sesuai'
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password berhasil diubah');
    }
}
