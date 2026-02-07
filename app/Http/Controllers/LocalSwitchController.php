<?php

namespace App\Http\Controllers;

use App\Models\Local;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LocalSwitchController extends Controller
{
    /**
     * Safely switches the user's active administration (Local).
     */
    public function switch(Request $request)
    {
        $request->validate([
            'local_id' => 'required|exists:locais,id',
        ]);

        $user = Auth::user();
        $targetLocalId = $request->local_id;

        // Security check: Is the user allowed to access this local?
        $authorizedIds = $user->authorized_locais->pluck('id')->toArray();

        if (!in_array($targetLocalId, $authorizedIds)) {
            return back()->with('error', 'Você não tem permissão para acessar esta administração.');
        }

        // Get local data for session
        $local = Local::findOrFail($targetLocalId);

        if (!$local->active) {
            return back()->with('error', 'Esta administração está inativa.');
        }

        // Update Session for TenancyMiddleware
        Session::put('current_local_id', $local->id);
        Session::put('current_local_name', $local->nome);
        Session::put('current_tenant_id', $local->id);
        Session::put('current_tenant_connection_data', $local);

        return back()->with('success', "Administração alterada para: {$local->nome}");
    }
}
