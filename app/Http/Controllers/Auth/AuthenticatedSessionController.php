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
        try {
            // Log 1: Login attempt start
            \Log::info('=== LOGIN ATTEMPT START ===');
            \Log::info('Request data:', [
                'username' => $request->username,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            // Log 2: Session sebelum login
            \Log::info('Before login - Session ID: ' . session()->getId());
            \Log::info('Session data before:', session()->all());
            
            // Log 3: Authenticate attempt
            \Log::info('Attempting authentication...');
            $request->authenticate();
            \Log::info('✓ Authentication successful');

            // Log 4: Session regenerate
            \Log::info('Regenerating session...');
            $request->session()->regenerate();
            \Log::info('✓ Session regenerated');

            // Log 5: Get authenticated user
            $user = Auth::user();
            \Log::info('After login - Session ID: ' . session()->getId());
            \Log::info('User logged in:', [
                'id' => $user->id_user,
                'username' => $user->username,
                'role' => $user->role,
                'nama' => $user->nama,
            ]);
            
            // Log 6: Determine redirect URL
            $redirectUrl = match($user->role) {
                'admin' => '/admin/dashboard',
                'petugas' => '/petugas/dashboard',
                default => '/peminjam/dashboard'
            };
            
            \Log::info('Redirect URL determined:', [
                'role' => $user->role,
                'url' => $redirectUrl
            ]);
            
            // Log 7: Before redirect
            \Log::info('Executing redirect to: ' . $redirectUrl);
            \Log::info('=== LOGIN ATTEMPT END SUCCESS ===');
            
            return redirect($redirectUrl);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Login validation failed (wrong credentials)
            \Log::warning('=== LOGIN FAILED - WRONG CREDENTIALS ===');
            \Log::warning('Username attempted: ' . $request->username);
            \Log::warning('Validation errors:', $e->errors());
            throw $e;
            
        } catch (\Exception $e) {
            // Other errors
            \Log::error('=== LOGIN FAILED - EXCEPTION ===');
            \Log::error('Error message: ' . $e->getMessage());
            \Log::error('Error file: ' . $e->getFile() . ':' . $e->getLine());
            \Log::error('Stack trace:', [
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors([
                'username' => 'Terjadi kesalahan saat login. Error: ' . $e->getMessage(),
            ])->withInput($request->only('username'));
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