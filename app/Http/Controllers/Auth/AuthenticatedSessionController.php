<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     * Triggers a full DB backup in the background before logging out.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->triggerBackgroundDbBackup();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Fire off db:backup artisan command in a detached background process.
     * Saves to D:\parampara\DB-Backup\ without blocking the logout response.
     */
    private function triggerBackgroundDbBackup(): void
    {
        $phpBinary = PHP_BINARY;
        $artisan   = base_path('artisan');

        // Windows: start /b launches a detached process — logout is not delayed
        $cmd = "start \"\" /b \"{$phpBinary}\" \"{$artisan}\" db:backup";

        popen($cmd, 'r');
    }
}
