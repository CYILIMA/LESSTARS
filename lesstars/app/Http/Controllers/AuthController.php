<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth', [
            'mode' => $request->routeIs('auth.login') ? 'login' : 'register',
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $email = strtolower(trim((string) $request->input('register_email', '')));
        $request->merge(['register_email' => $email]);

        $existingUser = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();
        $existingMember = $existingUser?->registration_number ? $existingUser : null;

        if ($existingUser && ! $existingMember) {
            throw ValidationException::withMessages([
                'register_email' => 'An account with this email already exists. Please sign in.',
            ]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'register_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'.($existingMember ? ','.$existingMember->id : '')],
            'password' => ['required', 'confirmed', 'min:8'],
            'registration_number' => [$existingMember ? 'required' : 'nullable', 'string', 'max:255'],
        ]);

        if ($existingMember) {
            if (! hash_equals($existingMember->registration_number, $data['registration_number'])) {
                throw ValidationException::withMessages([
                    'registration_number' => 'The registration number does not match this Member Star account.',
                ]);
            }

            $existingMember->update([
                'name' => $data['name'],
                'email' => $data['register_email'],
                'password' => $data['password'],
            ]);
            $user = $existingMember;
        } else {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['register_email'],
                'password' => $data['password'],
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login_email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt([
            'email' => $credentials['login_email'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        return back()
            ->withErrors(['login_email' => 'These credentials do not match our records.'])
            ->withInput($request->only('login_email'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
