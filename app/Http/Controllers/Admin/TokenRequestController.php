<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TokenV2;
use App\Models\Local;

class TokenRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Only Super Admin and Regional Coordinators can see/approve token requests
        if (!$user->isAdminSistema() && !$user->isAdminRegional()) {
            abort(403, 'Acesso não autorizado.');
        }

        $query = TokenV2::with(['user', 'local.regional'])
            ->where('ativo', 0)
            ->orderBy('created_at', 'desc');

        $solicitacoes = $query->paginate(15);
        
        // Fetch locales for the selection dropdown
        if ($user->isAdminSistema()) {
            $locais = Local::orderBy('adm_local')->get();
        } else {
            // Regional Coordinator can only assign locales from their regional
            $locais = Local::where('admrg_id', $user->regional_id)->orderBy('adm_local')->get();
        }

        return view('admin.token-requests.index', compact('solicitacoes', 'locais'));
    }

    public function approve(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->isAdminSistema() && !$user->isAdminRegional()) {
            abort(403, 'Acesso não autorizado.');
        }

        $token = TokenV2::findOrFail($id);

        if ($token->ativo || $token->admlc_id > 0) {
            return back()->with('error', 'Esta solicitação já foi processada.');
        }

        $validated = $request->validate([
            'admlc_id' => 'required|integer|exists:mysql_sys.admlcs_v2,admlc_id',
        ]);

        $admlc_id = $validated['admlc_id'];

        // If regional coordinator, verify they own the local
        if ($user->isAdminRegional()) {
            $local = Local::where('admlc_id', $admlc_id)->first();
            if (!$local || $local->admrg_id != $user->regional_id) {
                abort(403, 'Acesso não autorizado para esta administração local.');
            }
        }

        // Update token
        $token->update([
            'admlc_id' => $admlc_id,
            'ativo' => 1
        ]);

        // Update user
        if ($token->user) {
            $token->user->update([
                'admlc_id' => $admlc_id
            ]);
        }

        return back()->with('success', 'Solicitação aprovada e associada com sucesso.');
    }

    public function reject($id)
    {
        $user = Auth::user();

        if (!$user->isAdminSistema() && !$user->isAdminRegional()) {
            abort(403, 'Acesso não autorizado.');
        }

        $token = TokenV2::findOrFail($id);

        if ($token->ativo || $token->admlc_id > 0) {
            return back()->with('error', 'Esta solicitação já foi processada.');
        }

        // Deleting the pending/inactive token
        $token->delete();

        return back()->with('success', 'Solicitação rejeitada e removida com sucesso.');
    }
}
