<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TokenV2;

class DesktopApiController extends Controller
{
    public function storeSolicitacao(Request $request)
    {
        $validated = $request->validate([
            'dispositivo' => 'required|string|max:255',
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefone' => 'nullable|string|max:50',
            'igreja' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
        ]);

        $email = strtolower($validated['email']);
        $dispositivo = strtoupper($validated['dispositivo']);

        // Check if user exists by email
        $user = User::where('email', $email)->first();

        if ($user) {
            // Check if device already has a token for this user
            $deviceExists = TokenV2::where('dispositivo', $dispositivo)
                ->where('user_id', $user->id)
                ->first();

            if ($deviceExists) {
                return response()->json([
                    'success' => false,
                    'message' => "Já existe um token atribuído ao dispositivo {$dispositivo}."
                ], 422);
            }

            // Update user details
            $user->update([
                'telefone' => $validated['telefone'],
                'igreja' => strtoupper($validated['igreja']),
                'cidade' => strtoupper($validated['cidade'])
            ]);
        } else {
            // Create user
            $user = User::create([
                'name' => strtoupper($validated['nome']),
                'email' => $email,
                'telefone' => $validated['telefone'],
                'igreja' => strtoupper($validated['igreja']),
                'cidade' => strtoupper($validated['cidade']),
                'tipo' => 'user',
                'foto' => '/Imagens/userProfile.png'
            ]);
        }

        // Generate SHA-256 token hash (matching VB.NET logic)
        $tokenHash = hash('sha256', $email . $dispositivo);

        // Create pending token (ativo = 0, admlc_id = 0)
        $token = TokenV2::create([
            'token' => $tokenHash,
            'dispositivo' => $dispositivo,
            'user_id' => $user->id,
            'admlc_id' => 0,
            'ativo' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Solicitação registrada com sucesso! Aguarde contato do Administrador.',
            'data' => [
                'token' => $tokenHash,
                'user_id' => $user->id
            ]
        ], 201);
    }
}
