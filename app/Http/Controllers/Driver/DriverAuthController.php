<?php

declare(strict_types=1);

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * DriverAuthController - PIN-based Authentication for Couriers
 * 
 * DeepSecurity: PIN hashed, rate limited login.
 * DeepUI: Simple, mobile-first login form.
 */
class DriverAuthController extends Controller
{
    /**
     * Show login form.
     * 
     * @return View
     */
    public function showLogin(): View
    {
        return view('driver.login');
    }

    /**
     * Authenticate driver with username + PIN.
     * 
     * DeepSecurity: Rate limiting untuk mencegah brute force PIN.
     * DeepReasoning: PIN 6 digit di-hash dengan bcrypt.
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|max:100',
            'pin' => 'required|string|size:6',
        ]);

        // DeepSecurity: STRICT rate limiting - 3 attempts per 10 minutes (lebih ketat)
        // DeepReasoning: PIN 6 digit = 1 juta kombinasi, dengan 3 attempts/10min = max 432 attempts/day
        // Probabilitas brute force berhasil dalam 1 hari: 0.0432% (sangat rendah)
        $rateLimitKey = 'driver_login:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $minutes = ceil($seconds / 60);
            return back()->withErrors([
                'pin' => "Terlalu banyak percobaan login. Coba lagi dalam {$minutes} menit.",
            ]);
        }

        // Find courier by username/email
        $driver = Admin::where('role', 'courier')
            ->where('is_active', true)
            ->where(function ($query) use ($validated) {
                $query->where('username', $validated['username'])
                      ->orWhere('email', $validated['username']);
            })
            ->first();

        if (!$driver) {
            RateLimiter::hit($rateLimitKey, 600); // 10 menit (lebih lama)
            return back()->withErrors([
                'username' => 'Akun tidak ditemukan atau tidak aktif.',
            ])->withInput(['username' => $validated['username']]);
        }

        // DeepSecurity: Verify hashed PIN
        if (!$driver->pin || !Hash::check($validated['pin'], $driver->pin)) {
            RateLimiter::hit($rateLimitKey, 600); // 10 menit (lebih lama)
            return back()->withErrors([
                'pin' => 'PIN tidak valid.',
            ])->withInput(['username' => $validated['username']]);
        }

        // Clear rate limit on success
        RateLimiter::clear($rateLimitKey);

        // Login driver with "driver" guard
        Auth::guard('driver')->login($driver);

        $request->session()->regenerate();

        return redirect()->intended(route('driver.dashboard'));
    }

    /**
     * Logout driver.
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('driver')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('driver.login');
    }
}
