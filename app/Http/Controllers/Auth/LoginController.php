<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view("auth.login");
    }

    public function login(Request $request)
    {
        $request->validate([
            "email"    => ["required", "email"],
            "password" => ["required"],
        ]);

        $this->ensureIsNotRateLimited($request);

        if (Auth::attempt($request->only("email", "password"), $request->boolean("remember"))) {
            $request->session()->regenerate();
            RateLimiter::clear($this->throttleKey($request));
            session()->flash('login_success', true);
            session()->flash('login_user', auth()->user()->name);
            return redirect()->intended("/");
        }

        RateLimiter::hit($this->throttleKey($request), 60);

        throw ValidationException::withMessages([
            "email" => "The provided credentials do not match our records.",
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect("/login");
    }

    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            "email" => "Too many login attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return Str::lower($request->input("email")) . "|" . $request->ip();
    }
}
