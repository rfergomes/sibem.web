<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Local;
use App\Models\TokenV2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    private function checkAccess()
    {
        $user = Auth::user();
        if (!$user->isAdminSistema() && !$user->isAdminRegional() && !$user->isAdminLocal()) {
            abort(403, 'Acesso não autorizado.');
        }
    }

    private function getScopedUsersQuery()
    {
        $user = Auth::user();
        $query = User::with(['local.regional'])->orderBy('name');

        if ($user->isAdminSistema()) {
            return $query;
        } elseif ($user->isAdminRegional()) {
            $regionalId = $user->regional_id;
            return $query->whereHas('local', function ($q) use ($regionalId) {
                $q->where('admrg_id', $regionalId);
            });
        } else { // admin_local
            return $query->where('admlc_id', $user->admlc_id);
        }
    }

    public function index(Request $request)
    {
        $this->checkAccess();
        
        $search = $request->input('search');
        $query = $this->getScopedUsersQuery();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('igreja', 'like', "%{$search}%")
                  ->orWhere('cidade', 'like', "%{$search}%");
            });
        }

        $usuarios = $query->paginate(15);
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $this->checkAccess();
        $user = Auth::user();
        $locais = $user->getAvailableLocais();

        return view('admin.usuarios.create', compact('locais'));
    }

    public function store(Request $request)
    {
        $this->checkAccess();
        $currentUser = Auth::user();

        $rules = [
            'name' => 'required|string|max:200',
            'email' => 'required|email|max:100|unique:mysql_sys.users_v2,email',
            'telefone' => 'nullable|string|max:30',
            'password' => 'required|string|min:6|confirmed',
            'tipo' => 'required|string|in:admin_sistema,admin_regional,admin_local,operador,auditor',
            'admlc_id' => 'required|integer|exists:mysql_sys.admlcs_v2,admlc_id',
            'igreja' => 'nullable|string|max:200',
            'cidade' => 'nullable|string|max:200',
        ];

        // Access checks for user type and locale assignment
        if (!$currentUser->isAdminSistema()) {
            if ($request->tipo === 'admin_sistema') {
                return back()->withInput()->with('error', 'Você não pode criar administradores do sistema.');
            }
            if ($currentUser->isAdminRegional()) {
                if ($request->tipo === 'admin_regional') {
                    return back()->withInput()->with('error', 'Você não pode criar administradores regionais.');
                }
                // Verify local belongs to regional
                $local = Local::where('admlc_id', $request->admlc_id)->first();
                if (!$local || $local->admrg_id != $currentUser->regional_id) {
                    return back()->withInput()->with('error', 'Localidade selecionada inválida para sua regional.');
                }
            } else { // admin_local
                if (in_array($request->tipo, ['admin_sistema', 'admin_regional', 'admin_local'])) {
                    return back()->withInput()->with('error', 'Você só pode criar operadores ou auditores.');
                }
                if ($request->admlc_id != $currentUser->admlc_id) {
                    return back()->withInput()->with('error', 'Você só pode criar usuários no seu próprio local.');
                }
            }
        }

        $validated = $request->validate($rules);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'telefone' => $validated['telefone'],
            'password' => Hash::make($validated['password']),
            'tipo' => $validated['tipo'],
            'admlc_id' => $validated['admlc_id'],
            'igreja' => $validated['igreja'],
            'cidade' => $validated['cidade'],
        ]);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuário criado com sucesso.');
    }

    public function show($id)
    {
        $this->checkAccess();
        $usuario = User::with(['local.regional'])->findOrFail($id);

        // Security check on target user scope
        $currentUser = Auth::user();
        if (!$currentUser->isAdminSistema()) {
            if ($currentUser->isAdminRegional()) {
                if (!$usuario->local || $usuario->local->admrg_id != $currentUser->regional_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            } else { // admin_local
                if ($usuario->admlc_id != $currentUser->admlc_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            }
        }

        // Fetch tokens for this user
        $tokens = TokenV2::where('user_id', $usuario->id)->orderBy('created_at', 'desc')->get();

        return view('admin.usuarios.show', compact('usuario', 'tokens'));
    }

    public function edit($id)
    {
        $this->checkAccess();
        $usuario = User::findOrFail($id);

        $currentUser = Auth::user();
        if (!$currentUser->isAdminSistema()) {
            if ($currentUser->isAdminRegional()) {
                if (!$usuario->local || $usuario->local->admrg_id != $currentUser->regional_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            } else { // admin_local
                if ($usuario->admlc_id != $currentUser->admlc_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            }
        }

        $locais = $currentUser->getAvailableLocais();
        return view('admin.usuarios.edit', compact('usuario', 'locais'));
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess();
        $usuario = User::findOrFail($id);
        $currentUser = Auth::user();

        // Scope check
        if (!$currentUser->isAdminSistema()) {
            if ($currentUser->isAdminRegional()) {
                if (!$usuario->local || $usuario->local->admrg_id != $currentUser->regional_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            } else { // admin_local
                if ($usuario->admlc_id != $currentUser->admlc_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            }
        }

        $rules = [
            'name' => 'required|string|max:200',
            'email' => 'required|email|max:100|unique:mysql_sys.users_v2,email,' . $usuario->id,
            'telefone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6|confirmed',
            'tipo' => 'required|string|in:admin_sistema,admin_regional,admin_local,operador,auditor',
            'admlc_id' => 'required|integer|exists:mysql_sys.admlcs_v2,admlc_id',
            'igreja' => 'nullable|string|max:200',
            'cidade' => 'nullable|string|max:200',
        ];

        // Validate changes to role/locale
        if (!$currentUser->isAdminSistema()) {
            if ($request->tipo === 'admin_sistema') {
                return back()->withInput()->with('error', 'Você não pode promover usuários a admin do sistema.');
            }
            if ($currentUser->isAdminRegional()) {
                if ($request->tipo === 'admin_regional') {
                    return back()->withInput()->with('error', 'Você não pode criar ou alterar admin regional.');
                }
                $local = Local::where('admlc_id', $request->admlc_id)->first();
                if (!$local || $local->admrg_id != $currentUser->regional_id) {
                    return back()->withInput()->with('error', 'Localidade selecionada inválida para sua regional.');
                }
            } else { // admin_local
                if (in_array($request->tipo, ['admin_sistema', 'admin_regional', 'admin_local'])) {
                    return back()->withInput()->with('error', 'Você só pode manter usuários como operadores ou auditores.');
                }
                if ($request->admlc_id != $currentUser->admlc_id) {
                    return back()->withInput()->with('error', 'Você só pode associar usuários ao seu próprio local.');
                }
            }
        }

        $validated = $request->validate($rules);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'telefone' => $validated['telefone'],
            'tipo' => $validated['tipo'],
            'admlc_id' => $validated['admlc_id'],
            'igreja' => $validated['igreja'],
            'cidade' => $validated['cidade'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $usuario->update($data);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $this->checkAccess();
        $usuario = User::findOrFail($id);
        $currentUser = Auth::user();

        if ($usuario->id === $currentUser->id) {
            return back()->with('error', 'Você não pode excluir o seu próprio usuário.');
        }

        // Scope check
        if (!$currentUser->isAdminSistema()) {
            if ($currentUser->isAdminRegional()) {
                if (!$usuario->local || $usuario->local->admrg_id != $currentUser->regional_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            } else { // admin_local
                if ($usuario->admlc_id != $currentUser->admlc_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            }
        }

        // Revoke all tokens belonging to this user
        TokenV2::where('user_id', $usuario->id)->delete();

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuário e seus tokens de acesso excluídos com sucesso.');
    }

    public function generateToken(Request $request, $id)
    {
        $this->checkAccess();
        $usuario = User::findOrFail($id);
        $currentUser = Auth::user();

        // Scope check
        if (!$currentUser->isAdminSistema()) {
            if ($currentUser->isAdminRegional()) {
                if (!$usuario->local || $usuario->local->admrg_id != $currentUser->regional_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            } else { // admin_local
                if ($usuario->admlc_id != $currentUser->admlc_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            }
        }

        $validated = $request->validate([
            'dispositivo' => 'required|string|max:60',
        ]);

        $tokenStr = Str::random(40);

        TokenV2::create([
            'token' => $tokenStr,
            'dispositivo' => $validated['dispositivo'],
            'admlc_id' => $usuario->admlc_id,
            'user_id' => $usuario->id,
            'ativo' => 1 // Manually generated tokens are pre-approved
        ]);

        return back()->with('success', 'Novo token gerado com sucesso: ' . $tokenStr);
    }
}
