<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Igreja;
use App\Models\Local;
use App\Models\TipoImovel;
use App\Models\Setor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IgrejaController extends Controller
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
        $query = Igreja::with(['local.regional', 'tipoImovel'])->orderBy('igreja');

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
        $user = Auth::user();
        $search = $request->input('search');
        $query = $this->getScopedQuery();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('igreja', 'like', "%{$search}%")
                  ->orWhere('cod_siga', 'like', "%{$search}%")
                  ->orWhere('cnpj', 'like', "%{$search}%")
                  ->orWhere('cidade', 'like', "%{$search}%")
                  ->orWhere('uf', 'like', "%{$search}%");
            });
        }

        if ($request->filled('admlc_id')) {
            $query->where('admlc_id', $request->input('admlc_id'));
        }

        $availableLocais = $user->getAvailableLocais();
        $igrejas = $query->paginate(15)->withQueryString();

        return view('admin.igrejas.index', compact('igrejas', 'availableLocais'));
    }

    public function create()
    {
        $this->checkAccess('create');
        $user = Auth::user();
        $locais = $user->getAvailableLocais();
        $tiposImovel = TipoImovel::orderBy('nome')->get();
        
        // Fetch sectors scoped to what the user can see
        if ($user->isAdminSistema()) {
            $setores = Setor::orderBy('cod_setor')->get();
        } elseif ($user->isAdminRegional()) {
            $regionalId = $user->regional_id;
            $setores = Setor::whereHas('local', function ($q) use ($regionalId) {
                $q->where('admrg_id', $regionalId);
            })->orderBy('cod_setor')->get();
        } else {
            $setores = Setor::where('admlc_id', $user->admlc_id)->orderBy('cod_setor')->get();
        }

        return view('admin.igrejas.create', compact('locais', 'tiposImovel', 'setores'));
    }

    public function store(Request $request)
    {
        $this->checkAccess('create');
        $currentUser = Auth::user();

        $validated = $request->validate([
            'igreja_id' => 'required|string|max:11',
            'igreja' => 'required|string|max:200',
            'cod_siga' => 'required|string|max:20',
            'razao_social' => 'nullable|string|max:200',
            'cnpj' => 'nullable|string|max:20',
            'logradouro' => 'nullable|string|max:200',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:160',
            'cidade' => 'nullable|string|max:200',
            'uf' => 'nullable|string|max:2',
            'tipo_id' => 'nullable|integer|exists:mysql_sys.tipos_imovel,id',
            'status_id' => 'nullable|integer',
            'cod_setor' => 'nullable|string|max:11',
            'admlc_id' => 'required|integer|exists:mysql_sys.admlcs_v2,admlc_id',
            'observacao' => 'nullable|string|max:200',
        ]);

        // Check if church_id already exists for this local to satisfy unique constraint
        $exists = Igreja::where('igreja_id', $validated['igreja_id'])
                        ->where('admlc_id', $validated['admlc_id'])
                        ->exists();
        if ($exists) {
            return back()->withInput()->with('error', 'Já existe um templo com este ID nesta Administração Local.');
        }

        // Scope check for target local
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

        Igreja::create($validated);

        return redirect()->route('admin.igrejas.index')->with('success', 'Igreja criada com sucesso.');
    }

    public function show($id)
    {
        $this->checkAccess('view');
        $igreja = Igreja::with(['local.regional', 'tipoImovel'])->findOrFail($id);
        $currentUser = Auth::user();

        // Scope check
        if (!$currentUser->isAdminSistema()) {
            if ($currentUser->isAdminRegional()) {
                if (!$igreja->local || $igreja->local->admrg_id != $currentUser->regional_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            } else { // admin_local, operador, auditor
                if ($igreja->admlc_id != $currentUser->admlc_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            }
        }

        return view('admin.igrejas.show', compact('igreja'));
    }

    public function edit($id)
    {
        $this->checkAccess('edit');
        $igreja = Igreja::findOrFail($id);
        $currentUser = Auth::user();

        // Scope check
        if (!$currentUser->isAdminSistema()) {
            if ($currentUser->isAdminRegional()) {
                if (!$igreja->local || $igreja->local->admrg_id != $currentUser->regional_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            } else { // admin_local
                if ($igreja->admlc_id != $currentUser->admlc_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            }
        }

        $locais = $currentUser->getAvailableLocais();
        $tiposImovel = TipoImovel::orderBy('nome')->get();
        
        // Fetch sectors scoped to what the user can see
        if ($currentUser->isAdminSistema()) {
            $setores = Setor::orderBy('cod_setor')->get();
        } elseif ($currentUser->isAdminRegional()) {
            $regionalId = $currentUser->regional_id;
            $setores = Setor::whereHas('local', function ($q) use ($regionalId) {
                $q->where('admrg_id', $regionalId);
            })->orderBy('cod_setor')->get();
        } else {
            $setores = Setor::where('admlc_id', $currentUser->admlc_id)->orderBy('cod_setor')->get();
        }

        return view('admin.igrejas.edit', compact('igreja', 'locais', 'tiposImovel', 'setores'));
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess('edit');
        $igreja = Igreja::findOrFail($id);
        $currentUser = Auth::user();

        $validated = $request->validate([
            'igreja_id' => 'required|string|max:11',
            'igreja' => 'required|string|max:200',
            'cod_siga' => 'required|string|max:20',
            'razao_social' => 'nullable|string|max:200',
            'cnpj' => 'nullable|string|max:20',
            'logradouro' => 'nullable|string|max:200',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:160',
            'cidade' => 'nullable|string|max:200',
            'uf' => 'nullable|string|max:2',
            'tipo_id' => 'nullable|integer|exists:mysql_sys.tipos_imovel,id',
            'status_id' => 'nullable|integer',
            'cod_setor' => 'nullable|string|max:11',
            'admlc_id' => 'required|integer|exists:mysql_sys.admlcs_v2,admlc_id',
            'observacao' => 'nullable|string|max:200',
        ]);

        // Check unique constraint if igreja_id or admlc_id changed
        if ($igreja->igreja_id !== $validated['igreja_id'] || $igreja->admlc_id !== (int)$validated['admlc_id']) {
            $exists = Igreja::where('igreja_id', $validated['igreja_id'])
                            ->where('admlc_id', $validated['admlc_id'])
                            ->where('id', '!=', $igreja->id)
                            ->exists();
            if ($exists) {
                return back()->withInput()->with('error', 'Já existe um templo com este ID nesta Administração Local.');
            }
        }

        // Scope check for target and new locale
        if (!$currentUser->isAdminSistema()) {
            if ($currentUser->isAdminRegional()) {
                if (!$igreja->local || $igreja->local->admrg_id != $currentUser->regional_id) {
                    abort(403, 'Acesso não autorizado.');
                }
                $newLocal = Local::where('admlc_id', $validated['admlc_id'])->first();
                if (!$newLocal || $newLocal->admrg_id != $currentUser->regional_id) {
                    abort(403, 'Acesso não autorizado para localidade selecionada.');
                }
            } else { // admin_local
                if ($igreja->admlc_id != $currentUser->admlc_id || $validated['admlc_id'] != $currentUser->admlc_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            }
        }

        $igreja->update($validated);

        return redirect()->route('admin.igrejas.index')->with('success', 'Igreja atualizada com sucesso.');
    }

    public function destroy($id)
    {
        $this->checkAccess('delete');
        $igreja = Igreja::findOrFail($id);
        $currentUser = Auth::user();

        // Scope check
        if (!$currentUser->isAdminSistema()) {
            if ($currentUser->isAdminRegional()) {
                if (!$igreja->local || $igreja->local->admrg_id != $currentUser->regional_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            } else { // admin_local
                if ($igreja->admlc_id != $currentUser->admlc_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            }
        }

        // Note: churches can be referenced in tenant database (inventarios/bens), 
        // we can perform delete (Laravel will do DB cascading if set, or throw DB error if restrict)
        try {
            $igreja->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Não é possível excluir esta igreja pois ela pode possuir bens ou inventários ativos nos bancos locais.');
        }

        return redirect()->route('admin.igrejas.index')->with('success', 'Igreja excluída com sucesso.');
    }
}
