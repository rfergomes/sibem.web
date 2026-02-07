<?php

namespace App\Http\Controllers;

use App\Models\Local;
use App\Models\Regional;
use App\Services\TenantProvisioningService;
use Illuminate\Http\Request;

class LocalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locais = Local::with('regional')->paginate(15);
        return view('admin.locais.index', compact('locais'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regionais = Regional::all();
        return view('admin.locais.create', compact('regionais'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, TenantProvisioningService $provisioner)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'regional_id' => 'required|exists:regionais,id',
            'db_host' => 'required|string',
            'db_name' => 'required|string|unique:locais,db_name',
            'db_user' => 'required|string',
            'db_password' => 'nullable|string',
            'active' => 'boolean'
        ]);

        $local = Local::create($validated);

        if ($request->has('provision')) {
            try {
                $provisioner->provision($local);
                $message = 'Administração criada e banco de dados inicializado com sucesso!';
            } catch (\Exception $e) {
                $message = 'Administração criada, mas falhou ao inicializar banco: ' . $e->getMessage();
                return redirect()->route('locais.index')->with('warning', $message);
            }
        } else {
            $message = 'Administração criada com sucesso!';
        }

        return redirect()->route('locais.index')->with('success', $message);
    }

    /**
     * Provision an existing local.
     */
    public function provision(Local $local, TenantProvisioningService $provisioner)
    {
        try {
            $provisioner->provision($local);
            return back()->with('success', 'Banco de dados reinicializado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Falha ao inicializar banco: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Local $local)
    {
        $regionais = Regional::all();
        return view('admin.locais.edit', compact('local', 'regionais'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Local $local)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'regional_id' => 'required|exists:regionais,id',
            'db_host' => 'required|string',
            'db_name' => 'required|string|unique:locais,db_name,' . $local->id,
            'db_user' => 'required|string',
            'db_password' => 'nullable|string',
            'active' => 'boolean'
        ]);

        $local->update($validated);

        return redirect()->route('locais.index')->with('success', 'Administração atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Local $local)
    {
        $local->delete();
        return redirect()->route('locais.index')->with('success', 'Administração removida com sucesso!');
    }
}
