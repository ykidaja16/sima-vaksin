<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('role')
            ->orderBy('name')
            ->paginate(30);

        $roles = Role::where('is_active', true)->get();

        return view('users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ], [
            'name.required' => 'Nama wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'role_id.required' => 'Role wajib dipilih',
        ]);

        try {
            $user = User::create([
                'name' => $request->input('name'),
                'username' => $request->input('username'),
                'password' => Hash::make($request->input('password')),
                'role_id' => $request->input('role_id'),
                'is_active' => true,
            ]);

            /** @var User $currentUser */
            $currentUser = Auth::user();
            Log::info("User created by IT", [
                'user_id' => $user->id,
                'username' => $user->username,
                'role' => $user->role_name,
                'created_by' => $currentUser ? $currentUser->username : 'system',
            ]);

            return redirect()->route('users.index')
                ->with('success', "User {$user->username} berhasil ditambahkan");
        } catch (\Exception $e) {
            Log::error("Failed to create user: " . $e->getMessage());
            return redirect()->route('users.index')
                ->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Nama wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'role_id.required' => 'Role wajib dipilih',
        ]);

        try {
            $data = [
                'name' => $request->input('name'),
                'username' => $request->input('username'),
                'role_id' => $request->input('role_id'),
                'is_active' => $request->boolean('is_active', false),
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->input('password'));
            }

            $user->update($data);

            /** @var User $currentUser */
            $currentUser = Auth::user();
            Log::info("User updated by IT", [
                'user_id' => $user->id,
                'username' => $user->username,
                'updated_by' => $currentUser ? $currentUser->username : 'system',
            ]);

            return redirect()->route('users.index')
                ->with('success', "User {$user->username} berhasil diupdate");
        } catch (\Exception $e) {
            Log::error("Failed to update user: " . $e->getMessage());
            return redirect()->route('users.index')
                ->with('error', 'Gagal mengupdate user: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself
        /** @var User|null $currentUser */
        $currentUser = Auth::user();
        if ($user->id === ($currentUser ? $currentUser->id : null)) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri');
        }

        try {
            $username = $user->username;
            $user->delete();

            /** @var User $currentUser */
            $currentUser = Auth::user();
            Log::info("User deleted by IT", [
                'user_id' => $id,
                'username' => $username,
                'deleted_by' => $currentUser ? $currentUser->username : 'system',
            ]);

            return redirect()->route('users.index')
                ->with('success', "User {$username} berhasil dihapus");
        } catch (\Exception $e) {
            Log::error("Failed to delete user: " . $e->getMessage());
            return redirect()->route('users.index')
                ->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);

        // Prevent deactivating yourself
        /** @var User|null $currentUser */
        $currentUser = Auth::user();
        if ($user->id === ($currentUser ? $currentUser->id : null)) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun sendiri');
        }

        try {
            $user->update(['is_active' => !$user->is_active]);

            $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

            /** @var User $currentUser */
            $currentUser = Auth::user();
            Log::info("User {$status} by IT", [
                'user_id' => $user->id,
                'username' => $user->username,
                'status' => $user->is_active,
                'updated_by' => $currentUser ? $currentUser->username : 'system',
            ]);

            return redirect()->route('users.index')
                ->with('success', "User {$user->username} berhasil {$status}");
        } catch (\Exception $e) {
            Log::error("Failed to toggle user status: " . $e->getMessage());
            return redirect()->route('users.index')
                ->with('error', 'Gagal mengubah status user: ' . $e->getMessage());
        }
    }
}
