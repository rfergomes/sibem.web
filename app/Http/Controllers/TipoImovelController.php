<?php

namespace App\Http\Controllers;

use App\Models\TipoImovel;
use Illuminate\Http\Request;

class TipoImovelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', TipoImovel::class);

        $tipos = TipoImovel::orderBy('nome')->paginate(15);

        return view('admin.tipos_imovel.index', compact('tipos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', TipoImovel::class);

        return view('admin.tipos_imovel.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', TipoImovel::class);

        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:tipos_imovel,nome',
        ]);

        TipoImovel::create($validated);

        return redirect()->route('tipos-imovel.index')->with('success', 'Tipo de imóvel criado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoImovel $tiposImovel)
    {
        $this->authorize('update', $tiposImovel);

        return view('admin.tipos_imovel.edit', compact('tiposImovel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoImovel $tiposImovel)
    {
        $this->authorize('update', $tiposImovel);

        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:tipos_imovel,nome,' . $tiposImovel->id,
        ]);

        $tiposImovel->update($validated);

        return redirect()->route('tipos-imovel.index')->with('success', 'Tipo de imóvel atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoImovel $tiposImovel)
    {
        $this->authorize('delete', $tiposImovel);

        // Check if there are any churches using this type
        if ($tiposImovel->igrejas()->count() > 0) {
            return redirect()->route('tipos-imovel.index')
                ->with('error', 'Não é possível excluir este tipo pois existem localidades associadas a ele.');
        }

        $tiposImovel->delete();

        return redirect()->route('tipos-imovel.index')->with('success', 'Tipo de imóvel removido com sucesso!');
    }
}
