<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DependenciaController extends Controller
{
    public function index()
    {
        $dependencias = Dependencia::all();
        return view('admin.dependencias.index', compact('dependencias'));
    }

    public function create()
    {
        $this->authorize('create', Dependencia::class);
        return view('admin.dependencias.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Dependencia::class);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'active' => 'boolean'
        ]);

        Dependencia::create($validated);

        return redirect()->route('dependencias.index')->with('success', 'Dependência criada com sucesso.');
    }

    public function edit(Dependencia $dependencia)
    {
        $this->authorize('update', $dependencia);
        return view('admin.dependencias.edit', compact('dependencia'));
    }

    public function update(Request $request, Dependencia $dependencia)
    {
        $this->authorize('update', $dependencia);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'active' => 'boolean'
        ]);

        $dependencia->update($validated);

        return redirect()->route('dependencias.index')->with('success', 'Dependência atualizada com sucesso.');
    }

    public function destroy(Dependencia $dependencia)
    {
        $this->authorize('delete', $dependencia);
        $dependencia->delete();
        return redirect()->route('dependencias.index')->with('success', 'Dependência removida com sucesso.');
    }
}
