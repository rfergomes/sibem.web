<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Local;
use App\Models\Regional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocalController extends Controller
{
    private function checkAccess($action = 'view')
    {
        $user = Auth::user();
        if (!$user->isAdminSistema() && !$user->isAdminRegional() && !$user->isAdminLocal()) {
            abort(403, 'Acesso não autorizado.');
        }

        if ($action !== 'view' && !$user->isAdminSistema()) {
            abort(403, 'Apenas o administrador do sistema pode modificar as Administrações Locais.');
        }
    }

    public function index(Request $request)
    {
        $this->checkAccess('view');
        $currentUser = Auth::user();
        $search = $request->input('search');

        if ($currentUser->isAdminLocal()) {
            // Local Admin should go directly to their own show page or only see themselves
            return redirect()->route('admin.locais.show', $currentUser->admlc_id);
        }

        $query = Local::with('regional')->orderBy('adm_local');

        if ($currentUser->isAdminRegional()) {
            $query->where('admrg_id', $currentUser->regional_id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('adm_local', 'like', "%{$search}%")
                  ->orWhere('razao_social', 'like', "%{$search}%")
                  ->orWhere('cnpj', 'like', "%{$search}%")
                  ->orWhere('cidade', 'like', "%{$search}%");
            });
        }

        $locais = $query->paginate(15);
        return view('admin.locais.index', compact('locais'));
    }

    public function create()
    {
        $this->checkAccess('create');
        $regionais = Regional::orderBy('adm_regional')->get();
        return view('admin.locais.create', compact('regionais'));
    }

    public function store(Request $request)
    {
        $this->checkAccess('create');
        $validated = $request->validate([
            'admlc_id' => 'required|integer|unique:mysql_sys.admlcs_v2,admlc_id',
            'adm_local' => 'required|string|max:200',
            'razao_social' => 'required|string|max:200',
            'cnpj' => 'required|string|max:20',
            'cidade' => 'required|string|max:200',
            'uf' => 'required|string|max:2|min:2',
            'admrg_id' => 'required|integer|exists:mysql_sys.admrgs_v2,admrg_id',
            'status_id' => 'required|integer',
        ]);

        Local::create($validated);

        return redirect()->route('admin.locais.index')->with('success', 'Administração Local criada com sucesso.');
    }

    public function show($id)
    {
        $this->checkAccess('view');
        // Retrieve local by admlc_id
        $local = Local::with(['regional'])->where('admlc_id', $id)->firstOrFail();
        $currentUser = Auth::user();

        // Scope check
        if ($currentUser->isAdminRegional()) {
            if ($local->admrg_id != $currentUser->regional_id) {
                abort(403, 'Acesso não autorizado para localidade fora da sua regional.');
            }
        } elseif ($currentUser->isAdminLocal()) {
            if ($local->admlc_id != $currentUser->admlc_id) {
                abort(403, 'Acesso não autorizado para localidade de terceiros.');
            }
        }

        return view('admin.locais.show', compact('local'));
    }

    public function edit($id)
    {
        $this->checkAccess('edit');
        $local = Local::where('admlc_id', $id)->firstOrFail();
        $regionais = Regional::orderBy('adm_regional')->get();

        return view('admin.locais.edit', compact('local', 'regionais'));
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess('edit');
        $local = Local::where('admlc_id', $id)->firstOrFail();

        $validated = $request->validate([
            'admlc_id' => 'required|integer|unique:mysql_sys.admlcs_v2,admlc_id,' . $local->id,
            'adm_local' => 'required|string|max:200',
            'razao_social' => 'required|string|max:200',
            'cnpj' => 'required|string|max:20',
            'cidade' => 'required|string|max:200',
            'uf' => 'required|string|max:2|min:2',
            'admrg_id' => 'required|integer|exists:mysql_sys.admrgs_v2,admrg_id',
            'status_id' => 'required|integer',
        ]);

        $local->update($validated);

        return redirect()->route('admin.locais.index')->with('success', 'Administração Local atualizada com sucesso.');
    }

    public function destroy($id)
    {
        $this->checkAccess('delete');
        $local = Local::where('admlc_id', $id)->firstOrFail();

        // Check if there are churches or users belonging to it
        $userCount = \App\Models\User::where('admlc_id', $local->admlc_id)->count();
        $igrejaCount = \App\Models\Igreja::where('admlc_id', $local->admlc_id)->count();

        if ($userCount > 0 || $igrejaCount > 0) {
            return back()->with('error', 'Não é possível excluir esta localidade pois existem usuários ou igrejas associados a ela.');
        }

        $local->delete();

        return redirect()->route('admin.locais.index')->with('success', 'Administração Local excluída com sucesso.');
    }
}
