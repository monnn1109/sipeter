<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', '!=', 'admin');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip_nidn', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name', 'asc')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'required|in:dosen,staff',
            'nip_nidn' => 'required|string|max:50',
            'phone' => 'required|string|max:20',
            'unit' => 'required|string|max:255',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'nip_nidn' => $validated['nip_nidn'],
            'phone' => $validated['phone'],
            'unit' => $validated['unit'],
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function edit($id)
    {
        $user = User::where('role', '!=', 'admin')->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::where('role', '!=', 'admin')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'nip_nidn' => 'required|string|max:50',
            'phone' => 'required|string|max:20',
            'unit' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate');
    }

    public function destroy($id)
    {
        $user = User::where('role', '!=', 'admin')->findOrFail($id);

        if ($user->documentRequests()->count() > 0) {
            return back()->with('error', 'User tidak bisa dihapus karena memiliki pengajuan dokumen');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus');
    }

    public function resetPassword($id)
    {
        $user = User::where('role', '!=', 'admin')->findOrFail($id);

        $newPassword = 'password';

        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        return back()->with('success', 'Password berhasil direset ke: ' . $newPassword);
    }
}
