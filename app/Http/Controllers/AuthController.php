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

            // Priority 1: Use user's default_local_id if set
            if ($user->default_local_id) {
                $localToSelect = DB::table('locais')->where('id', $user->default_local_id)->where('active', true)->first();
            }

            // Priority 2: Use user's local_id if set and authorized
            if (!$localToSelect && $user->local_id) {
                $isAuthorized = false;

                if ($user->perfil_id == 1) {
                    $isAuthorized = true;
                } elseif ($user->perfil_id == 2) {
                    $targetLocal = DB::table('locais')->where('id', $user->local_id)->first();
                    if ($targetLocal && $targetLocal->regional_id == $user->regional_id) {
                        $isAuthorized = true;
                    }
                } else {
                    $isAuthorized = $user->locais()->where('locais.id', $user->local_id)->exists();
                }

                if ($isAuthorized) {
                    $localToSelect = DB::table('locais')->where('id', $user->local_id)->where('active', true)->first();
                }
            }

            // Priority 3: Pick the first authorized local
            if (!$localToSelect) {
                $firstLocal = $user->authorized_locais->first();
                if ($firstLocal) {
                    $localToSelect = DB::table('locais')->where('id', $firstLocal->id)->first();
                }
            }

            if ($localToSelect) {
                // Ensure we have the full local object with regional info
                if (!isset($localToSelect->regional_id) || !isset($localToSelect->nome)) {
                    $localToSelect = \App\Models\Local::with('regional')->find($localToSelect->id);
                }

                // Fetch Regional Name
                $regionalName = 'Regional';
                if (isset($localToSelect->regional)) {
                    $regionalName = $localToSelect->regional->nome;
                } elseif (isset($localToSelect->regional_id)) {
                    $regional = DB::table('regionais')->where('id', $localToSelect->regional_id)->first();
                    if ($regional) {
                        $regionalName = $regional->nome;
                    }
                }

                Session::put('current_local_id', $localToSelect->id);
                Session::put('current_local_name', $localToSelect->nome);
                Session::put('current_regional_name', $regionalName);
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
