<?php

namespace App\Http\Controllers;

use App\Models\Setor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetorController extends Controller
{
    public function index()
    {
        // Scoped to current tenant automatically via connection, but we should double check local_id logic if needed.
        // For standard tenant structure, selects from 'setores'.
        $setores = Setor::all();
        return view('admin.setores.index', compact('setores'));
    }

    public function create()
    {
        $this->authorize('create', Setor::class);
        return view('admin.setores.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Setor::class);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'active' => 'boolean'
        ]);

        // Assuming current tenant context implies local_id or related logic
        // But the schema implies 'local_id' exists. 
        // If we are in Tenant context, 'local_id' usually refers to the ID in the 'locais' table.
        // However, in a multi-tenant single-db or hybrid, we might need to set it explicitly.
        // Given existing Setor model fillable ['local_id', 'nome', 'active'], we need to set local_id.
        // But wait, Setor is in Tenant DB. Does Tenant DB have 'locais' table? 
        // Usually Tenant DB is specific to ONE local. So local_id might be redundant or fixed.
        // Let's assume for now we don't need to prompt for local_id if we are IN that local's DB context.
        // Or we might need to pass the Global Local ID.

        $validated['local_id'] = session('current_local_id');

        Setor::create($validated);

        return redirect()->route('setores.index')->with('success', 'Setor criado com sucesso.');
    }

    public function edit(Setor $setor)
    {
        $this->authorize('update', $setor);
        return view('admin.setores.edit', compact('setor'));
    }

    public function update(Request $request, Setor $setor)
    {
        $this->authorize('update', $setor);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'active' => 'boolean'
        ]);

        $setor->update($validated);

        return redirect()->route('setores.index')->with('success', 'Setor atualizado com sucesso.');
    }

    public function destroy(Setor $setor)
    {
        $this->authorize('delete', $setor);
        $setor->delete();
        return redirect()->route('setores.index')->with('success', 'Setor removido com sucesso.');
    }
}
