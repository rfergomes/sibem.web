<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dependencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DependenciaController extends Controller
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
        $query = Dependencia::orderBy('descricao');

        if ($search) {
            $query->where('descricao', 'like', "%{$search}%");
        }

        $dependencias = $query->paginate(15);
        return view('admin.dependencias.index', compact('dependencias'));
    }

    public function create()
    {
        $this->checkAccess('create');
        return view('admin.dependencias.create');
    }

    public function store(Request $request)
    {
        $this->checkAccess('create');
        $validated = $request->validate([
            'dependencia_id' => 'required|integer|unique:mysql_sys.dependencias_v2,dependencia_id',
            'descricao' => 'required|string|max:250',
        ]);

        Dependencia::create($validated);

        return redirect()->route('admin.dependencias.index')->with('success', 'Dependência criada com sucesso.');
    }

    public function show($id)
    {
        $this->checkAccess('view');
        $dependencia = Dependencia::findOrFail($id);
        return view('admin.dependencias.show', compact('dependencia'));
    }

    public function edit($id)
    {
        $this->checkAccess('edit');
        $dependencia = Dependencia::findOrFail($id);
        return view('admin.dependencias.edit', compact('dependencia'));
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess('edit');
        $dependencia = Dependencia::findOrFail($id);

        $validated = $request->validate([
            'dependencia_id' => 'required|integer|unique:mysql_sys.dependencias_v2,dependencia_id,' . $dependencia->id,
            'descricao' => 'required|string|max:250',
        ]);

        $dependencia->update($validated);

        return redirect()->route('admin.dependencias.index')->with('success', 'Dependência atualizada com sucesso.');
    }

    public function destroy($id)
    {
        $this->checkAccess('delete');
        $dependencia = Dependencia::findOrFail($id);

        // Check if there are assets referencing it in tenants (can skip or warn)
        // Since it's global, we can check if it has references (in legacy structure it might be linked to bens, but here we delete cleanly)
        $dependencia->delete();

        return redirect()->route('admin.dependencias.index')->with('success', 'Dependência excluída com sucesso.');
    }
}
