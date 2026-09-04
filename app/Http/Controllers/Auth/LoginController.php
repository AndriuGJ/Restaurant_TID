<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Configuration\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login', ['company' => Company::query()->first()]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $key = 'login:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'login' => 'Demasiados intentos. Inténtalo de nuevo en '.RateLimiter::availableIn($key).' segundos.',
            ]);
        }

        $credentials = $request->only('password');
        $login = $request->validated()['login'];

        $user = User::where('email', $login)
            ->orWhere('username', $login)
            ->first();

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials[$field] = $login;

        if ($user === null || ! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($key);

            throw ValidationException::withMessages([
                'login' => 'Las credenciales no coinciden con nuestros registros.',
            ]);
        }

        RateLimiter::clear($key);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect(route('login'));
    }
}
