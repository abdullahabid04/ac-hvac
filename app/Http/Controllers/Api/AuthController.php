<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = 'api-login|'.Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return $this->error('RATE_LIMITED', 'Too many login attempts. Retry later.', [], 429);
        }

        if (! Auth::attempt($credentials, false)) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages(['email' => 'The provided credentials do not match our records.']);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();
        $request->user()->update(['last_login_at' => now()]);
        $organization = $request->user()->currentOrganization();

        return $this->ok([
            'user' => $request->user()->only(['id', 'name', 'email', 'job_title']),
            'organization' => $organization?->only(['id', 'name', 'slug', 'timezone']),
        ], ['session' => 'cookie', 'csrf' => csrf_token()]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->ok(['logged_out' => true]);
    }
}
