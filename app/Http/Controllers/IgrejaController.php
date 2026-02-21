<?php

namespace App\Http\Controllers;

use App\Models\Igreja;
use App\Models\Local;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class IgrejaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Igreja::query();

        // 1. Authorization Scopes & Filters
        if ($user->perfil_id == 1) {
            // Admin Sistema: View All
            if ($request->filled('regional_id')) {
                $query->whereHas('local', function ($q) use ($request) {
                    $q->where('regional_id', $request->regional_id);
                });
            }
        } elseif ($user->perfil_id == 2) {
            // Admin Regional: View only their Regional
            // Check if user has regional_id, otherwise fail safe or view nothing?
            // Assuming user->regional_id is set for this profile.
            $query->whereHas('local', function ($q) use ($user) {
                $q->where('regional_id', $user->regional_id);
            });
        } else {
            // Admin Local / Operador: View only Authorized Locais
            // authorized_locais returns a collection, getting IDs is better for query
            $localIds = $user->authorized_locais->pluck('id');
            $query->whereIn('local_id', $localIds);
        }

        // 2. Common Filters
        if ($request->filled('local_id')) {
            // Ensure the user is actually authorized to see this local if they are restricted
            // The Access Scope above acts as a base, but strictly:
            // If I am Admin Local for ID 10 and 11, and I request local_id=12, the query->whereIn('local_id', [10,11]) AND where('local_id', 12) will return nothing. 
            // So we can just add the where clause safely.
            $query->where('local_id', $request->local_id);
        }

        if ($request->filled('uf')) {
            $query->where('uf', $request->uf);
        }

        if ($request->filled('tipo')) {
            $query->where('id_tipo', $request->tipo);
        }

        if ($request->filled('status')) {
            $query->where('id_status', $request->status);
        }

        // 3. Advanced Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhere('codigo_ccb', 'like', "%{$search}%")
                    ->orWhere('legacy_id', 'like', "%{$search}%") // "BR 22-0317" or "DR"
                    ->orWhere('cidade', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%"); // Direct ID search
            });
        }

        // 4. Sorting
        $query->orderBy('nome');

        // 5. Data for View
        $igrejas = $query->with(['local.regional', 'tipoImovel'])->paginate(15)->withQueryString();

        // Data for Filters
        $regionais = [];
        if ($user->perfil_id == 1) {
            $regionais = \App\Models\Regional::where('active', true)->orderBy('nome')->get();
        }

        // Locais for Filter:
        // If Admin Sistema: All active locales (or filtered by selected regional via JS/AJAX? For now simple list or all)
        // If Admin Regional: All in their regional
        // If Others: authorized_locais
        $locais = collect([]);
        if ($user->perfil_id == 1) {
            // Perf optimization: Maybe only show if regional selected? 
            // For now, let's load all to populate the dropdown, or maybe limit?
            // "Locais" can be huge (960+). Loading all might be heavy for the DOM.
            // Let's rely on standard collection for now, user asked for optimization but we can refining later.
            $locais = \App\Models\Local::where('active', true)->orderBy('nome')->get();
        } else {
            $locais = $user->authorized_locais;
        }

        // Fetch Normalized Building Types
        $tipos = \App\Models\TipoImovel::orderBy('nome')->pluck('nome', 'id');

        $ufs = $this->getUfs();

        return view('admin.igrejas.index', compact('igrejas', 'tipos', 'regionais', 'locais', 'ufs'));
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
        $tipos = \App\Models\TipoImovel::orderBy('nome')->get();

        return view('admin.igrejas.create', compact('locais', 'setores', 'ufs', 'tipos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Igreja::class);
        $user = Auth::user();

        $validated = $request->validate([
            'local_id' => 'required|exists:locais,id',
            'codigo_ccb' => 'required|string|max:20',
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
            'id_tipo' => 'required|integer', // Added validation for type
        ]);

        // Authorization Check for Local ID
        if ($user->perfil_id == 2) {
            $local = Local::find($validated['local_id']);
            if ($local->regional_id != $user->regional_id) {
                abort(403, 'Você não pode criar igrejas fora da sua regional.');
            }
        } elseif ($user->perfil_id == 3) {
            if (!$user->authorized_locais->contains($validated['local_id'])) {
                abort(403, 'Você não tem permissão para criar igrejas nesta administração.');
            }
        }

        // Strict UF Hierarchy Validation
        // "Uma igreja de campinas... sua administração obrigatóriamente estará na uf SP"
        $local = Local::with('regional')->find($validated['local_id']);

        // Check 1: Igreja UF vs Local UF
        if ($local->uf && $local->uf != $validated['uf']) {
            return back()->withErrors(['uf' => "A UF da Igreja ({$validated['uf']}) deve ser igual à UF da Administração ({$local->uf})."])->withInput();
        }

        // Check 2: Local UF vs Regional UF
        if ($local->regional && $local->regional->uf && $local->regional->uf != $validated['uf']) {
            return back()->withErrors(['uf' => "A UF da Igreja ({$validated['uf']}) deve respeitar a UF da Regional ({$local->regional->uf})."])->withInput();
        }

        $validated['id_status'] = 1; // Default active
        // $validated['id_tipo'] is now validated

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
        $tipos = \App\Models\TipoImovel::orderBy('nome')->get();

        // Capture previous URL to preserve filters
        $redirect_to = url()->previous();

        return view('admin.igrejas.edit', compact('igreja', 'locais', 'setores', 'ufs', 'tipos', 'redirect_to'));
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
            'codigo_ccb' => 'required|string|max:20',
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

        // Redirect to the preserved URL (with filters) or default index
        $redirect_to = $request->input('redirect_to', route('igrejas.index'));
        return redirect($redirect_to)->with('success', 'Igreja atualizada com sucesso.');
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
