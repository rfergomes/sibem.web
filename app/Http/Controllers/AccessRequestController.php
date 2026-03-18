<?php

namespace App\Http\Controllers;


use App\Models\SolicitacaoAcesso;
use App\Models\Regional;
use App\Models\User;
use App\Models\Perfil;
use App\Mail\NewAccessRequestNotification;
use App\Mail\AccessApproved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AccessRequestController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regionais = Regional::orderBy('nome')->get();
        return view('auth.access-request', compact('regionais'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email|unique:solicitacao_acessos,email',
            'telefone' => 'nullable|string|max:20',
            'cidade' => 'required|string|max:100',
            'regional_id' => 'nullable|exists:regionais,id',
            'observacoes' => 'nullable|string',
        ]);

        $solicitacao = SolicitacaoAcesso::create($validated);

        // Create notifications for admins
        app(\App\Services\NotificationService::class)->createAccessRequestNotification($solicitacao);

        // Notify Admins via Email
        // Assuming 'admin' slug for administrators. Adjust as needed.
        $admins = User::whereHas('perfil', function ($q) {
            $q->where('slug', 'admin')->orWhere('slug', 'administrador');
        })->get();

        if ($admins->isEmpty()) {
            // Fallback: Notify a specific email or log warning
            // Mail::to('admin@example.com')->send(new NewAccessRequestNotification($solicitacao));
        } else {
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new NewAccessRequestNotification($solicitacao));
            }
        }

        return redirect()->route('login')->with('success', 'Solicitação de acesso enviada com sucesso! Aguarde aprovação.');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $solicitacoes = SolicitacaoAcesso::where('status', 'pending')->orderBy('created_at', 'desc')->get();
        return view('admin.access-requests.index', compact('solicitacoes'));
    }

    /**
     * Approve the specified resource.
     */
    public function approve(Request $request, $id)
    {
        $solicitacao = SolicitacaoAcesso::findOrFail($id);

        if ($solicitacao->status !== 'pending') {
            return redirect()->back()->with('error', 'Solicitação já processada.');
        }

        // Generate Password
        $password = Str::random(10);

        // Find or Create User
        // Use transaction? Yes.

        $user = User::create([
            'nome' => $solicitacao->nome,
            'email' => $solicitacao->email,
            'password' => Hash::make($password),
            // Default Perfil? 'usuario' or similar. 
            // We need to decide what profile to give.
            // For now, let's assume a default ID or handle it.
            // Let's assume passed in request or default to a safe 'user' profile found by slug.
            'perfil_id' => Perfil::where('slug', 'operador')->first()->id ?? 4, // Fallback ID 4 (assuming operator)
            'regional_id' => $solicitacao->regional_id,
        ]);

        // Link User to Regional/Local possibly? 
        // For now just basic user creation.

        $solicitacao->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        // Send Email with Credentials
        Mail::to($user->email)->send(new AccessApproved($user, $password));

        // Create notification for the user
        app(\App\Services\NotificationService::class)->createAccessRequestStatusNotification($solicitacao, 'approved');

        return redirect()->route('admin.access-requests.index')->with('success', 'Acesso aprovado e credenciais enviadas.');
    }

    /**
     * Reject the specified resource.
     */
    public function reject($id)
    {
        $solicitacao = SolicitacaoAcesso::findOrFail($id);

        if ($solicitacao->status !== 'pending') {
            return redirect()->back()->with('error', 'Solicitação já processada.');
        }

        $solicitacao->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
        ]);

        // Option: Send Rejection Email

        // Create notification for the user (if person has a user already or if we want to notify them upon next login attempt/mail)
        // SolicitacaoAcesso has email. If we want to notify via system, they need to be able to login.
        // If rejected, they can't login, so system notification is only useful if they were already a user.
        // But the plan says "Sua solicitação foi indeferida".
        // Let's assume we create it anyway in case they have a partial account or for audit.
        app(\App\Services\NotificationService::class)->createAccessRequestStatusNotification($solicitacao, 'rejected');

        return redirect()->route('admin.access-requests.index')->with('success', 'Solicitação rejeitada.');
    }
}
