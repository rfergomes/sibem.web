<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TokenV2;
use App\Models\User;
use App\Models\Local;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TokenController extends Controller
{
    private function checkAccess()
    {
        $user = Auth::user();
        if (!$user->isAdminSistema() && !$user->isAdminRegional() && !$user->isAdminLocal()) {
            abort(403, 'Acesso não autorizado.');
        }
    }

    private function getScopedTokensQuery()
    {
        $user = Auth::user();
        $query = TokenV2::with(['user', 'local.regional'])->orderBy('created_at', 'desc');

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
        $currentUser = Auth::user();
        
        $search = $request->input('search');
        $query = $this->getScopedTokensQuery();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('token', 'like', "%{$search}%")
                  ->orWhere('dispositivo', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $tokens = $query->paginate(15);

        // Fetch users for the dropdown to create tokens
        if ($currentUser->isAdminSistema()) {
            $users = User::orderBy('name')->get();
        } elseif ($currentUser->isAdminRegional()) {
            $regionalId = $currentUser->regional_id;
            $users = User::whereHas('local', function ($q) use ($regionalId) {
                $q->where('admrg_id', $regionalId);
            })->orderBy('name')->get();
        } else {
            $users = User::where('admlc_id', $currentUser->admlc_id)->orderBy('name')->get();
        }

        return view('admin.tokens.index', compact('tokens', 'users'));
    }

    public function store(Request $request)
    {
        $this->checkAccess();
        $currentUser = Auth::user();

        $validated = $request->validate([
            'user_id' => 'required|integer|exists:mysql_sys.users_v2,id',
            'dispositivo' => 'required|string|max:60',
        ]);

        $targetUser = User::findOrFail($validated['user_id']);

        // Scope check for target user
        if (!$currentUser->isAdminSistema()) {
            if ($currentUser->isAdminRegional()) {
                if (!$targetUser->local || $targetUser->local->admrg_id != $currentUser->regional_id) {
                    abort(403, 'Acesso não autorizado para este usuário.');
                }
            } else { // admin_local
                if ($targetUser->admlc_id != $currentUser->admlc_id) {
                    abort(403, 'Acesso não autorizado para este usuário.');
                }
            }
        }

        $tokenStr = Str::random(40);

        TokenV2::create([
            'token' => $tokenStr,
            'dispositivo' => $validated['dispositivo'],
            'admlc_id' => $targetUser->admlc_id,
            'user_id' => $targetUser->id,
            'ativo' => 1
        ]);

        return back()->with('success', "Token gerado com sucesso para {$targetUser->name}: " . $tokenStr);
    }

    public function destroy($id)
    {
        $this->checkAccess();
        $token = TokenV2::findOrFail($id);
        $currentUser = Auth::user();

        // Scope check
        if (!$currentUser->isAdminSistema()) {
            if ($currentUser->isAdminRegional()) {
                if (!$token->local || $token->local->admrg_id != $currentUser->regional_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            } else { // admin_local
                if ($token->admlc_id != $currentUser->admlc_id) {
                    abort(403, 'Acesso não autorizado.');
                }
            }
        }

        $token->delete();

        return back()->with('success', 'Token de acesso revogado e removido com sucesso.');
    }
}
