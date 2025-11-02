<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
 * Handle an incoming authentication request.
 */
public function store(LoginRequest $request): RedirectResponse
{
    // Debug session sebelum login
    \Log::info('Before login - Session ID: ' . session()->getId());
    
    $request->authenticate();

    $request->session()->regenerate();

    $user = Auth::user();
    
    // Debug session setelah login
    \Log::info('After login - Session ID: ' . session()->getId());
    \Log::info('User logged in:', [
        'id' => $user->id_user,
        'username' => $user->username,
        'role' => $user->role
    ]);
    
    // Redirect berdasarkan role - PAKAI URL LANGSUNG
    if ($user->role === 'admin') {
        return redirect('/admin/dashboard');
    } elseif ($user->role === 'petugas') {
        return redirect('/petugas/dashboard');
    } else {
        return redirect('/peminjam/dashboard');
    }
}

        /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        // Regenerate session token untuk keamanan
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke halaman utama setelah logout
        return redirect('/');
    }
}