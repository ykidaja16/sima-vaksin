<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        if (!Auth::check()) {
            Log::warning('Unauthenticated user tried to access protected route', [
                'route' => $request->route()->getName(),
                'ip' => $request->ip(),
            ]);
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        $user = Auth::user();

        // Check if user is active
        if (!$user->is_active) {
            Log::warning('Inactive user tried to access protected route', [
                'user_id' => $user->id,
                'username' => $user->username,
                'route' => $request->route()->getName(),
            ]);
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Akun Anda tidak aktif. Hubungi administrator.');
        }

        // Check role
        $roleName = $user->role?->nama_role;

        if ($role === 'it' && $roleName !== 'IT') {
            Log::warning('User without IT role tried to access IT route', [
                'user_id' => $user->id,
                'username' => $user->username,
                'route' => $request->route()->getName(),
            ]);
            abort(403, 'Akses ditolak. Hanya user IT yang dapat mengakses halaman ini.');
        }

        if ($role === 'admin' && $roleName !== 'Admin') {
            Log::warning('User without Admin role tried to access Admin route', [
                'user_id' => $user->id,
                'username' => $user->username,
                'route' => $request->route()->getName(),
            ]);
            abort(403, 'Akses ditolak. Hanya user Admin yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
