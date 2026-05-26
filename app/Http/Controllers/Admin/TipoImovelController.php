<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoImovel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TipoImovelController extends Controller
{
    private function checkAccess($action = 'view')
    {
        $user = Auth::user();
        if ($action !== 'view') {
            if (!$user->isAdminSistema()) {
                abort(403, 'Acesso restrito apenas ao administrador do sistema.');
            }
        }
    }

    public function index(Request $request)
    {
        $this->checkAccess('view');
        $search = $request->input('search');
        $query = TipoImovel::orderBy('nome');

        if ($search) {
            $query->where('nome', 'like', "%{$search}%");
        }

        $tiposImovel = $query->paginate(15);
        return view('admin.tipos-imovel.index', compact('tiposImovel'));
    }

    public function create()
    {
        $this->checkAccess('create');
        return view('admin.tipos-imovel.create');
    }

    public function store(Request $request)
    {
        $this->checkAccess('create');
        $validated = $request->validate([
            'nome' => 'required|string|max:100|unique:mysql_sys.tipos_imovel,nome',
        ]);

        TipoImovel::create($validated);

        return redirect()->route('admin.tipos-imovel.index')->with('success', 'Tipo de Imóvel criado com sucesso.');
    }

    public function show($id)
    {
        $this->checkAccess('view');
        $tipoImovel = TipoImovel::with('igrejas')->findOrFail($id);
        return view('admin.tipos-imovel.show', compact('tipoImovel'));
    }

    public function edit($id)
    {
        $this->checkAccess('edit');
        $tipoImovel = TipoImovel::findOrFail($id);
        return view('admin.tipos-imovel.edit', compact('tipoImovel'));
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess('edit');
        $tipoImovel = TipoImovel::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:100|unique:mysql_sys.tipos_imovel,nome,' . $tipoImovel->id,
        ]);

        $tipoImovel->update($validated);

        return redirect()->route('admin.tipos-imovel.index')->with('success', 'Tipo de Imóvel atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $this->checkAccess('delete');
        $tipoImovel = TipoImovel::findOrFail($id);

        if ($tipoImovel->igrejas()->count() > 0) {
            return back()->with('error', 'Não é possível excluir este tipo de imóvel pois existem igrejas associadas a ele.');
        }

        $tipoImovel->delete();

        return redirect()->route('admin.tipos-imovel.index')->with('success', 'Tipo de Imóvel excluído com sucesso.');
    }
}
