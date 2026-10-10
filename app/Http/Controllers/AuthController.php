<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $role = Auth::user()->role;
            $name = Auth::user()->name;

            return match ($role) {
                'admin_gudang' => redirect()->route('admin.dashboard')
                    ->with('success', "Selamat datang kembali, {$name}!"),
                'kasir' => redirect()->route('kasir.dashboard')
                    ->with('success', "Selamat datang di Panel Kasir, {$name}!"),
                'petugas' => redirect()->route('petugas.dashboard')
                    ->with('success', "Selamat datang di Panel Frontdesk, {$name}!"),
                'montor' => redirect()->route('montir.dashboard')
                    ->with('success', "Selamat datang, Mekanik {$name}!"),
                default => redirect()->intended('/dashboard')
                    ->with('success', "Berhasil login sebagai {$role}."),
            };
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah berhasil logout.');
    }
}
