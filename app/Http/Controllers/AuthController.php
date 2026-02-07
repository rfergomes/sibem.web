<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
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
            $user = Auth::user();

            if (!$user->active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Usuário inativo. Contate o suporte.']);
            }

            // Improved Multi-Tenant Session Initialization
            $localToSelect = null;

            if ($user->local_id) {
                // User has a fixed primary local
                $localToSelect = DB::table('locais')->where('id', $user->local_id)->where('active', true)->first();
            } else {
                // Admin or Regional: Pick the first authorized local as default
                $firstLocal = $user->authorized_locais->first();
                if ($firstLocal) {
                    $localToSelect = DB::table('locais')->where('id', $firstLocal->id)->first();
                }
            }

            if ($localToSelect) {
                Session::put('current_local_id', $localToSelect->id);
                Session::put('current_local_name', $localToSelect->nome);
                Session::put('current_tenant_id', $localToSelect->id);
                Session::put('current_tenant_connection_data', $localToSelect);
            }

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas estão incorretas.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
