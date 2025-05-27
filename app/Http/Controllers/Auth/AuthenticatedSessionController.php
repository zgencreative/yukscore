<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse|JsonResponse // Ubah tipe return
    {
        try {
            $request->authenticate(); // Ini akan melempar ValidationException jika gagal
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(), // Pesan error validasi default
                    'errors' => $e->errors(),    // Detail error per field
                ], 422); // Status 422 Unprocessable Entity
            }
            throw $e; // Lemparkan kembali jika bukan AJAX untuk penanganan error default Laravel
        }

        $request->session()->regenerate();

        // Jika permintaan mengharapkan JSON (AJAX)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil!',
                // Anda bisa menyertakan URL redirect jika perlu dihandle oleh JS
                'redirect_url' => session()->pull('url.intended', route('index', absolute: false))
            ]);
        }

        // Jika bukan AJAX, lakukan redirect seperti biasa
        return redirect()->intended(route('index', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
