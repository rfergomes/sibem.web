<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Perfil;
use App\Models\Regional;
use App\Models\Local;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\AccessApproved;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $query = User::with(['perfil', 'regional', 'locais']);
        $currentUser = auth()->user();

        // Admin Regional (2) - Only see users in their regional
        if ($currentUser->perfil_id == 2) {
            $query->where('regional_id', $currentUser->regional_id); // Users directly in regional
            // OR users who manage locals inside this regional (more complex, but usually regional_id is set on user)
            // For now, assume users created by Reg Admin inherit regional_id.
        }

        $users = $query->paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', User::class);

        $perfis = Perfil::where('active', true)->get();
        $regionais = Regional::where('active', true)->orderBy('nome')->get();
        $locais = Local::where('active', true)->orderBy('nome')->get();

        return view('users.create', compact('perfis', 'regionais', 'locais'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        // Validation logic modification for auto-generate
        $rules = [
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'perfil_id' => 'required|exists:perfis,id',
            'regional_id' => 'nullable|exists:regionais,id',
            'locais' => 'nullable|array',
            'locais.*' => 'exists:locais,id',
        ];

        if (!$request->has('auto_generate_password')) {
            $rules['password'] = 'required|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        // Handle Password Generation
        $plainPassword = $request->input('password');
        $mustChangePassword = false;

        if ($request->has('auto_generate_password')) {
            $plainPassword = Str::random(10);
            $mustChangePassword = true;
        }

        $validated['password'] = Hash::make($plainPassword);
        $validated['active'] = true;
        $validated['must_change_password'] = $mustChangePassword;

        $user = User::create($validated);

        if ($request->has('locais')) {
            $user->locais()->sync($request->locais);
        }

        // Send Email if Auto-Generated
        if ($request->has('auto_generate_password')) {
            Mail::to($user->email)->send(new AccessApproved($user, $plainPassword));
            $message = 'Usuário criado com sucesso! As credenciais foram enviadas por e-mail.';
        } else {
            $message = 'Usuário criado com sucesso!';
        }

        return redirect()->route('users.index')->with('success', $message);
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $perfis = Perfil::where('active', true)->get();
        $regionais = Regional::where('active', true)->orderBy('nome')->get();
        $locais = Local::where('active', true)->orderBy('nome')->get();

        return view('users.edit', compact('user', 'perfis', 'regionais', 'locais'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'perfil_id' => 'required|exists:perfis,id',
            'regional_id' => 'nullable|exists:regionais,id',
            'locais' => 'nullable|array',
            'locais.*' => 'exists:locais,id',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        // Sync Locals
        if ($request->has('locais')) {
            $user->locais()->sync($request->locais);
        } else {
            $user->locais()->detach(); // If empty, remove all
        }

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        // Don't allow self-delete
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Você não pode excluir a si mesmo.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuário removido com sucesso!');
    }
}
