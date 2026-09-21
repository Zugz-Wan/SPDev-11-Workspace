<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            if ($user->isPetugas()) {
                return redirect()->intended(route('officer.reports.index'));
            }

            return redirect()->intended(route('reports.index'));
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Quick login helper for demo/testing purpose.
     */
    public function quickLogin(Request $request, string $role): RedirectResponse
    {
        $user = User::where('role', $role)->first();

        if (! $user) {
            $user = User::create([
                'name' => ucfirst($role).' Demo',
                'email' => "{$role}@example.com",
                'password' => Hash::make('password'),
                'role' => $role,
                'status_akun' => 'terverifikasi',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->isPetugas()) {
            return redirect()->route('officer.reports.index')->with('success', "Login cepat berhasil sebagai {$user->name} ({$user->role})");
        }

        return redirect()->route('reports.index')->with('success', "Login cepat berhasil sebagai {$user->name} ({$user->role})");
    }
}
