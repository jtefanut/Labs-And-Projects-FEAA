<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function loginForm(): View
    {
        return view('auth.login');
    }

    public function registerForm(): View
    {
        return view('auth.register');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Datele de autentificare nu sunt corecte.'])->onlyInput('email');
        }

        if (! $request->user()->is_active) {
            Auth::logout();

            return back()->withErrors(['email' => 'Contul este dezactivat. Contactează echipa de administrare.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return to_route(match ($request->user()->role?->name) {
            'admin' => 'admin.dashboard',
            'payment_operator' => 'operator.payments',
            default => 'client.dashboard',
        });
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', 'unique:users,email'],
            'company' => ['nullable', 'string', 'max:160'],
            'password' => ['required', 'string', 'min:10', 'confirmed'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $clientRole = Role::where('name', 'client')->firstOrFail();
            $user = User::create([
                'name' => $validated['name'], 'email' => $validated['email'],
                'password' => $validated['password'], 'role_id' => $clientRole->id,
            ]);
            Client::create(['user_id' => $user->id, 'company' => $validated['company'] ?? null]);
            AuditLog::create(['user_id' => $user->id, 'action' => 'Client account created', 'entity_type' => 'client']);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return to_route('client.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('home');
    }
}
