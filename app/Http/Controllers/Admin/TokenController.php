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

        $locais = $currentUser->getAvailableLocais();

        return view('admin.tokens.index', compact('tokens', 'users', 'locais'));
    }

    public function store(Request $request)
    {
        $this->checkAccess();
        $currentUser = Auth::user();

        $validated = $request->validate([
            'user_id' => 'required|integer|exists:mysql_sys.users_v2,id',
            'dispositivo' => 'required|string|max:60',
            'admlc_id' => 'required|integer|exists:mysql_sys.admlcs_v2,admlc_id',
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

        // Scope check for selected admlc_id
        if (!$currentUser->isAdminSistema()) {
            if ($currentUser->isAdminRegional()) {
                $selectedLocal = Local::where('admlc_id', $validated['admlc_id'])->first();
                if (!$selectedLocal || $selectedLocal->admrg_id != $currentUser->regional_id) {
                    return back()->withInput()->with('error', 'A administração selecionada não pertence à sua regional.');
                }
            } else { // admin_local
                if ($validated['admlc_id'] != $currentUser->admlc_id) {
                    return back()->withInput()->with('error', 'Você só pode gerar tokens para a sua própria administração.');
                }
            }
        }

        $tokenStr = Str::random(40);

        TokenV2::create([
            'token' => $tokenStr,
            'dispositivo' => $validated['dispositivo'],
            'admlc_id' => $validated['admlc_id'],
            'user_id' => $targetUser->id,
            'ativo' => 1
        ]);

        return back()->with('success', "Token gerado com sucesso para {$targetUser->name}: " . $tokenStr);
    }

    public function sendEmail($id)
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

        $user = $token->user;
        if (!$user || !$user->email) {
            return back()->with('error', 'O usuário associado a este token não possui e-mail cadastrado.');
        }

        $tokenStr = $token->token;
        $mensagem = "A Paz de Deus!\nSegue token para acesso ao sistema de inventários SIBEM CCB\n\n" . $tokenStr;

        try {
            \Illuminate\Support\Facades\Mail::raw($mensagem, function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Token de Acesso Desktop - SIBEM CCB');
            });
            return back()->with('success', 'Token enviado por e-mail com sucesso!');
        } catch (\Exception $e) {
            logger()->error("Erro ao enviar e-mail de token: " . $e->getMessage());
            return back()->with('error', 'Falha ao enviar e-mail. Verifique a configuração de SMTP.');
        }
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
