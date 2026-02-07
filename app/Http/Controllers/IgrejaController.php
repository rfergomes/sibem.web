<?php

namespace App\Http\Controllers;

use App\Models\Igreja;
use App\Models\Local;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IgrejaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Igreja::query();

        // 1. Authorization Scopes
        if ($user->perfil_id == 2 && $user->regional_id) {
            $query->whereHas('local', function ($q) use ($user) {
                $q->where('regional_id', $user->regional_id);
            });
        } elseif ($user->perfil_id > 2) {
            $localIds = $user->authorized_locais->pluck('id');
            $query->whereIn('local_id', $localIds);
        }

        // 2. Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhere('codigo_ccb', 'like', "%{$search}%")
                    ->orWhere('cidade', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('id_tipo', $request->tipo);
        }

        if ($request->filled('status')) {
            $query->where('id_status', $request->status);
        }

        // 3. Sorting (Default to Name)
        $query->orderBy('nome');

        $igrejas = $query->with('local')->paginate(15)->withQueryString();

        // Pass types for filter dropdown
        // Assuming simple hardcoded types for now if table doesn't exist or is complex
        $tipos = [
            1 => 'Igreja',
            2 => 'Barracão',
            3 => 'Serralheria',
            4 => 'Oficina de Costura',
            5 => 'Outros'
        ];

        return view('admin.igrejas.index', compact('igrejas', 'tipos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Igreja::class);

        $locais = Auth::user()->authorized_locais;

        // Fetch sectors from current tenant context
        // Note: This relies on the user being connected to the correct tenant to see relevant sectors
        $setores = \App\Models\Setor::where('active', true)->orderBy('nome')->get();

        $ufs = $this->getUfs();

        return view('admin.igrejas.create', compact('locais', 'setores', 'ufs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Igreja::class);

        $validated = $request->validate([
            'local_id' => 'required|exists:locais,id',
            'codigo_ccb' => 'required|string|max:20|unique:igrejas_global,codigo_ccb',
            'nome' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'setor' => 'nullable|string|max:100',
            'razao_social' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:20',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'uf' => 'required|string|max:2',
            'observacao' => 'nullable|string',
        ]);

        $validated['id_status'] = 1; // Default active
        $validated['id_tipo'] = 1;   // Default type

        Igreja::create($validated);

        return redirect()->route('igrejas.index')->with('success', 'Localidade cadastrada com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Igreja $igreja)
    {
        $this->authorize('update', $igreja);

        $locais = Auth::user()->authorized_locais;
        $setores = \App\Models\Setor::where('active', true)->orderBy('nome')->get();
        $ufs = $this->getUfs();

        return view('admin.igrejas.edit', compact('igreja', 'locais', 'setores', 'ufs'));
    }

    private function getUfs()
    {
        return [
            ['sigla' => 'AC', 'nome' => 'Acre'],
            ['sigla' => 'AL', 'nome' => 'Alagoas'],
            ['sigla' => 'AP', 'nome' => 'Amapá'],
            ['sigla' => 'AM', 'nome' => 'Amazonas'],
            ['sigla' => 'BA', 'nome' => 'Bahia'],
            ['sigla' => 'CE', 'nome' => 'Ceará'],
            ['sigla' => 'DF', 'nome' => 'Distrito Federal'],
            ['sigla' => 'ES', 'nome' => 'Espírito Santo'],
            ['sigla' => 'GO', 'nome' => 'Goiás'],
            ['sigla' => 'MA', 'nome' => 'Maranhão'],
            ['sigla' => 'MT', 'nome' => 'Mato Grosso'],
            ['sigla' => 'MS', 'nome' => 'Mato Grosso do Sul'],
            ['sigla' => 'MG', 'nome' => 'Minas Gerais'],
            ['sigla' => 'PA', 'nome' => 'Pará'],
            ['sigla' => 'PB', 'nome' => 'Paraíba'],
            ['sigla' => 'PR', 'nome' => 'Paraná'],
            ['sigla' => 'PE', 'nome' => 'Pernambuco'],
            ['sigla' => 'PI', 'nome' => 'Piauí'],
            ['sigla' => 'RJ', 'nome' => 'Rio de Janeiro'],
            ['sigla' => 'RN', 'nome' => 'Rio Grande do Norte'],
            ['sigla' => 'RS', 'nome' => 'Rio Grande do Sul'],
            ['sigla' => 'RO', 'nome' => 'Rondônia'],
            ['sigla' => 'RR', 'nome' => 'Roraima'],
            ['sigla' => 'SC', 'nome' => 'Santa Catarina'],
            ['sigla' => 'SP', 'nome' => 'São Paulo'],
            ['sigla' => 'SE', 'nome' => 'Sergipe'],
            ['sigla' => 'TO', 'nome' => 'Tocantins'],
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Igreja $igreja)
    {
        $this->authorize('update', $igreja);

        $validated = $request->validate([
            'local_id' => 'required|exists:locais,id',
            'codigo_ccb' => 'required|string|max:20|unique:igrejas_global,codigo_ccb,' . $igreja->id,
            'nome' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'setor' => 'nullable|string|max:100',
            'razao_social' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:20',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'uf' => 'required|string|max:2',
            'observacao' => 'nullable|string',
        ]);

        $igreja->update($validated);

        return redirect()->route('igrejas.index')->with('success', 'Igreja atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Igreja $igreja)
    {
        $this->authorize('delete', $igreja);

        $igreja->delete();

        return redirect()->route('igrejas.index')->with('success', 'Igreja removida com sucesso.');
    }
}
