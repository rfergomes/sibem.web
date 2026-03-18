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

        $currentUser = auth()->user();
        $perfis = Perfil::where('active', true)->get();

        if ($currentUser->perfil_id == 2) {
            // Admin Regional: Can only assign to their regional
            $regionais = Regional::where('id', $currentUser->regional_id)->get();
            // And only locals in their regional
            $locais = Local::where('regional_id', $currentUser->regional_id)
                ->where('active', true)
                ->orderBy('nome')
                ->get();
        } else {
            // Admin Sistema: All
            $regionais = Regional::where('active', true)->orderBy('nome')->get();
            $locais = Local::with('regional')->where('active', true)->orderBy('nome')->get();
        }

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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'active' => 'nullable|boolean',
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
        $validated['active'] = $request->has('active') ? true : false;
        $validated['must_change_password'] = $mustChangePassword;

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create($validated);

        if ($request->has('locais')) {
            $user->locais()->sync($request->locais);
        }

        // Send Email if Auto-Generated
        if ($request->has('auto_generate_password')) {
            try {
                Mail::to($user->email)->send(new AccessApproved($user, $plainPassword));
                $message = 'Usuário criado com sucesso! As credenciais foram enviadas por e-mail.';
            } catch (\Exception $e) {
                // Log error but don't fail request
                $message = 'Usuário criado, mas falhou ao enviar e-mail: ' . $e->getMessage();
            }
        } else {
            $message = 'Usuário criado com sucesso!';
        }

        return redirect()->route('users.index')->with('success', $message);
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $currentUser = auth()->user();
        $perfis = Perfil::where('active', true)->get();

        if ($currentUser->perfil_id == 2) {
            // Admin Regional
            $regionais = Regional::where('id', $currentUser->regional_id)->get();
            $locais = Local::where('regional_id', $currentUser->regional_id)
                ->where('active', true)
                ->orderBy('nome')
                ->get();
        } else {
            // Admin Sistema
            $regionais = Regional::where('active', true)->orderBy('nome')->get();
            // Optimize filtering: Eager load regional to avoid N+1 in view
            $locais = Local::with('regional')->where('active', true)->orderBy('nome')->get();
        }

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
            'default_local_id' => 'nullable|exists:locais,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'remove_avatar' => 'nullable|boolean',
            'active' => 'nullable|boolean',
        ]);

        // Handle checkbox value (unchecked checkboxes don't send value)
        $validated['active'] = $request->has('active') ? true : false;

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                \Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Handle Remove Avatar
        if ($request->has('remove_avatar') && $request->remove_avatar) {
            if ($user->avatar) {
                \Storage::disk('public')->delete($user->avatar);
                $validated['avatar'] = null;
            }
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

        // Delete avatar file if exists
        if ($user->avatar) {
            \Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuário removido com sucesso!');
    }
}
