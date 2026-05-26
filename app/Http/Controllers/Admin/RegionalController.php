<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Regional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegionalController extends Controller
{
    private function checkAccess()
    {
        $user = Auth::user();
        if (!$user->isAdminSistema()) {
            abort(403, 'Acesso restrito apenas ao administrador do sistema.');
        }
    }

    public function index(Request $request)
    {
        $this->checkAccess();
        $search = $request->input('search');
        $query = Regional::orderBy('adm_regional');

        if ($search) {
            $query->where('adm_regional', 'like', "%{$search}%")
                  ->orWhere('uf', 'like', "%{$search}%");
        }

        $regionais = $query->paginate(15);
        return view('admin.regionais.index', compact('regionais'));
    }

    public function create()
    {
        $this->checkAccess();
        return view('admin.regionais.create');
    }

    public function store(Request $request)
    {
        $this->checkAccess();
        $validated = $request->validate([
            'admrg_id' => 'required|integer|unique:mysql_sys.admrgs_v2,admrg_id',
            'adm_regional' => 'required|string|max:200',
            'uf' => 'required|string|max:2|min:2',
        ]);

        Regional::create($validated);

        return redirect()->route('admin.regionais.index')->with('success', 'Administração Regional criada com sucesso.');
    }

    public function show($id)
    {
        $this->checkAccess();
        $regional = Regional::with(['locais'])->findOrFail($id);
        return view('admin.regionais.show', compact('regional'));
    }

    public function edit($id)
    {
        $this->checkAccess();
        $regional = Regional::findOrFail($id);
        return view('admin.regionais.edit', compact('regional'));
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess();
        $regional = Regional::findOrFail($id);

        $validated = $request->validate([
            'admrg_id' => 'required|integer|unique:mysql_sys.admrgs_v2,admrg_id,' . $regional->id,
            'adm_regional' => 'required|string|max:200',
            'uf' => 'required|string|max:2|min:2',
        ]);

        $regional->update($validated);

        return redirect()->route('admin.regionais.index')->with('success', 'Administração Regional atualizada com sucesso.');
    }

    public function destroy($id)
    {
        $this->checkAccess();
        $regional = Regional::findOrFail($id);

        if ($regional->locais()->count() > 0) {
            return back()->with('error', 'Não é possível excluir esta regional pois ela possui Administrações Locais associadas.');
        }

        $regional->delete();

        return redirect()->route('admin.regionais.index')->with('success', 'Administração Regional excluída com sucesso.');
    }
}
