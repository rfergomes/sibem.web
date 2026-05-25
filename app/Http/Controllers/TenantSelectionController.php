<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Local;

class TenantSelectionController extends Controller
{
    public function select(Request $request)
    {
        $request->validate([
            'local_id' => 'required|exists:mysql_sys.admlcs_v2,admlc_id'
        ]);

        $user = Auth::user();
        $localId = $request->input('local_id');

        // Verify if user is allowed to switch to this local
        if ($user->isAdminSistema()) {
            // Super Admin can switch to any local
            session()->put('active_admlc_id', $localId);
            return back()->with('success', 'Administração Local alterada com sucesso.');
        } elseif ($user->isAdminRegional()) {
            // Regional Coordinator can only switch to locals within their regional
            $local = Local::find($localId);
            if ($local && $local->regional_id == $user->regional_id) {
                session()->put('active_admlc_id', $localId);
                return back()->with('success', 'Administração Local alterada com sucesso.');
            }
        }

        return back()->with('error', 'Acesso negado para a administração selecionada.');
    }
}
