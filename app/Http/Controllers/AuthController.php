<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('patients.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string|max:50',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        // Check if user exists and is active
        $user = \App\Models\User::where('username', $credentials['username'])->first();

        if (!$user) {
            Log::warning('Login failed: User not found', ['username' => $credentials['username']]);
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->onlyInput('username');
        }

        if (!$user->is_active) {
            Log::warning('Login failed: User is inactive', ['username' => $credentials['username']]);
            return back()->withErrors([
                'username' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ])->onlyInput('username');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            Log::info('User logged in successfully', [
                'username' => $user->username,
                'role' => $user->role_name,
            ]);

            // Redirect based on role
            if ($user->isIT()) {
                return redirect()->route('users.index');
            }

            return redirect()->route('patients.index');
        }

        Log::warning('Login failed: Invalid credentials', ['username' => $credentials['username']]);

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out', ['username' => $user?->username]);

        return redirect()->route('login');
    }
}
