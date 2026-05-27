<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setor;
use App\Models\Local;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetorController extends Controller
{
    private function checkAccess($action = 'view')
    {
        $user = Auth::user();
        if ($action !== 'view') {
            if (!$user->isAdminSistema() && !$user->isAdminRegional() && !$user->isAdminLocal()) {
                abort(403, 'Acesso não autorizado.');
            }
        }
    }

    private function getScopedQuery()
    {
        $user = Auth::user();
        $query = Setor::with('local.regional')->withCount('igrejas')->orderBy('cod_setor');

        if ($user->isAdminSistema()) {
            return $query;
        } elseif ($user->isAdminRegional()) {
            $regionalId = $user->regional_id;
            return $query->whereHas('local', function ($q) use ($regionalId) {
                $q->where('admrg_id', $regionalId);
            });
        } else { // admin_local, operador, auditor
            return $query->where('admlc_id', $user->admlc_id);
        }
    }

    public function index(Request $request)
    {
        $this->checkAccess('view');
        $search = $request->input('search');
        $query = $this->getScopedQuery();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('cod_setor', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%");
            });
        }

        $setores = $query->paginate(15);
        return view('admin.setores.index', compact('setores'));
    }

    public function create()
    {
        $this->checkAccess('create');
        $user = Auth::user();
        $locais = $user->getAvailableLocais();

        return view('admin.setores.create', compact('locais'));
    }

    public function store(Request $request)
    {
        $this->checkAccess('create');
        $currentUser = Auth::user();

        $validated = $request->validate([
            'cod_setor' => 'required|string|max:3',
            'descricao' => 'required|string|max:60',
            'admlc_id' => 'required|integer|exists:mysql_sys.admlcs_v2,admlc_id',
        ]);

        // Scope check for write operation
        if (!$currentUser->isAdminSistema()) {
            if ($currentUser->isAdminRegional()) {
                $local = Local::where('admlc_id', $validated['admlc_id'])->first();
                if (!$local || $local->admrg_id != $currentUser->regional_id) {
                    abort(403, 'Acesso não autorizado para localidade fora da sua regional.');
                }
            } else { // admin_local
                if ($validated['admlc_id'] != $currentUser->admlc_id) {
                    abort(403, 'Acesso não autorizado para localidade de terceiros.');
                }
            }
        }

        Setor::create($validated);

        return redirect()->route('admin.setores.index')->with('success', 'Setor criado com sucesso.');
    }

    public function show($id)
    {
        $this->checkAccess('view');
        $setor = Setor::with('local.regional')->findOrFail($id);
        $currentUser = Auth::user();

        // Scope check
        if (!$currentUser->isAdminSistema()) {
            if ($currentUser->isAdminRegional()) {
                if (!$setor->local || $setor->local->admrg_id != $currentUser->regional_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            } else { // admin_local, operador, auditor
                if ($setor->admlc_id != $currentUser->admlc_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            }
        }

        return view('admin.setores.show', compact('setor'));
    }

    public function edit($id)
    {
        $this->checkAccess('edit');
        $setor = Setor::findOrFail($id);
        $currentUser = Auth::user();

        // Scope check
        if (!$currentUser->isAdminSistema()) {
            if ($currentUser->isAdminRegional()) {
                if (!$setor->local || $setor->local->admrg_id != $currentUser->regional_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            } else { // admin_local
                if ($setor->admlc_id != $currentUser->admlc_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            }
        }

        $locais = $currentUser->getAvailableLocais();
        return view('admin.setores.edit', compact('setor', 'locais'));
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess('edit');
        $setor = Setor::findOrFail($id);
        $currentUser = Auth::user();

        $validated = $request->validate([
            'cod_setor' => 'required|string|max:3',
            'descricao' => 'required|string|max:60',
            'admlc_id' => 'required|integer|exists:mysql_sys.admlcs_v2,admlc_id',
        ]);

        // Scope check for target and new locale
        if (!$currentUser->isAdminSistema()) {
            if ($currentUser->isAdminRegional()) {
                if (!$setor->local || $setor->local->admrg_id != $currentUser->regional_id) {
                    abort(403, 'Acesso não autorizado.');
                }
                $newLocal = Local::where('admlc_id', $validated['admlc_id'])->first();
                if (!$newLocal || $newLocal->admrg_id != $currentUser->regional_id) {
                    abort(403, 'Acesso não autorizado para localidade selecionada.');
                }
            } else { // admin_local
                if ($setor->admlc_id != $currentUser->admlc_id || $validated['admlc_id'] != $currentUser->admlc_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            }
        }

        $setor->update($validated);

        return redirect()->route('admin.setores.index')->with('success', 'Setor atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $this->checkAccess('delete');
        $setor = Setor::findOrFail($id);
        $currentUser = Auth::user();

        // Scope check
        if (!$currentUser->isAdminSistema()) {
            if ($currentUser->isAdminRegional()) {
                if (!$setor->local || $setor->local->admrg_id != $currentUser->regional_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            } else { // admin_local
                if ($setor->admlc_id != $currentUser->admlc_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            }
        }

        $setor->delete();

        return redirect()->route('admin.setores.index')->with('success', 'Setor excluído com sucesso.');
    }
}
