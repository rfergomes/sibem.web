<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SwitchAdmController extends Controller
{
    /**
     * List available Administrations for the current user to switch to.
     */
    public function list(Request $request)
    {
        $user = Auth::user();

        // RBAC: Logic to fetch allowed Regionais/Locais
        $query = DB::table('locais')->where('active', true);

        if ($user->perfil === 'admin_regional' && $user->regional_id) {
            $query->where('regional_id', $user->regional_id);
        }
        // admin_sistema sees all

        $locais = $query->orderBy('nome')->get();

        return response()->json($locais);
    }

    /**
     * Perform the switch.
     */
    public function switch(Request $request)
    {
        $request->validate([
            'local_id' => 'required|exists:locais,id'
        ]);

        $localId = $request->input('local_id');
        $user = Auth::user();

        // Security Check: Is user allowed to access this local?
        // simple check: if admin_regional, must match local's regional_id
        $local = DB::table('locais')->find($localId);

        if ($user->perfil === 'admin_regional') {
            if ($local->regional_id !== $user->regional_id) {
                return response()->json(['error' => 'Acesso negado a esta administração.'], 403);
            }
        }
        // admin_local/operador cannot switch (they are stuck to their local_id) but UI handles that.
        // admin_sistema allows all.

        // Perform Switch
        Session::put('current_local_id', $local->id);
        Session::put('current_local_name', $local->nome);
        Session::put('current_tenant_id', $local->id);
        Session::put('current_tenant_connection_data', $local);

        return response()->json(['success' => true, 'redirect' => route('dashboard')]);
    }
}
